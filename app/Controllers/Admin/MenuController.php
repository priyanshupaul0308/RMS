<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\MenuCategoryModel;
use App\Models\MenuItemModel;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * MenuController – Menu Categories, Dishes, and Live 86 Availability Toggle.
 */
class MenuController extends BaseController
{
    protected MenuCategoryModel $categoryModel;
    protected MenuItemModel     $itemModel;

    public function initController(
        \CodeIgniter\HTTP\RequestInterface  $request,
        \CodeIgniter\HTTP\ResponseInterface $response,
        \Psr\Log\LoggerInterface            $logger
    ): void {
        parent::initController($request, $response, $logger);
        $this->categoryModel = new MenuCategoryModel();
        $this->itemModel     = new MenuItemModel();
    }

    /**
     * Categories list.
     */
    public function categories(): string
    {
        $this->authorize('menu_categories.view');

        $categories = $this->categoryModel->getActiveCategoriesWithCount($this->currentRestaurantId);

        return $this->renderView('admin/menu/categories', [
            'pageTitle'  => 'Menu Categories',
            'categories' => $categories,
        ]);
    }

    /**
     * Menu items list with category filter.
     */
    public function items(): string
    {
        $this->authorize('menu_items.view');

        $categoryId = (int) $this->request->getGet('category_id');
        $search     = $this->request->getGet('q');

        $items      = $this->itemModel->getItemsForAdmin($categoryId > 0 ? $categoryId : null, $search);
        $categories = $this->categoryModel->getActiveCategoriesWithCount($this->currentRestaurantId);

        $db = db_connect();
        $taxRates = $db->table('tax_rates')->where('is_active', 1)->get()->getResultArray();

        return $this->renderView('admin/menu/items', [
            'pageTitle'         => 'Menu Items Catalog',
            'items'             => $items,
            'categories'        => $categories,
            'taxRates'          => $taxRates,
            'currentCategoryId' => $categoryId,
            'search'            => $search,
        ]);
    }

    /**
     * Store new menu item / dish.
     */
    public function storeItem(): ResponseInterface
    {
        if (!$this->can('menu_items.create') && $this->currentRoleSlug !== 'admin' && $this->currentRoleSlug !== 'manager') {
            $this->authorize('menu_items.create');
        }

        $rules = [
            'name'        => 'required|min_length[2]|max_length[150]',
            'category_id' => 'required|is_natural_no_zero',
            'price'       => 'required|numeric',
        ];

        if (!$this->validate($rules)) {
            if ($this->request->isAJAX()) {
                return $this->jsonError('Validation failed', 422, $this->validator->getErrors());
            }
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $name       = trim((string) $this->request->getPost('name'));
        $categoryId = (int) $this->request->getPost('category_id');
        $code       = trim((string) $this->request->getPost('code'));
        $price      = (float) $this->request->getPost('price');
        $desc       = trim((string) $this->request->getPost('description'));
        $taxRateId  = $this->request->getPost('tax_rate_id') ? (int) $this->request->getPost('tax_rate_id') : null;
        $isVeg      = (int) ($this->request->getPost('is_veg') ?? 1);
        $prepTime   = (int) ($this->request->getPost('preparation_time') ?: 15);
        $sortOrder  = (int) ($this->request->getPost('sort_order') ?? 0);
        $isAvail    = (int) ($this->request->getPost('is_available') ?? 1);

        // Auto-generate dish code if empty
        if ($code === '') {
            $category = $this->categoryModel->find($categoryId);
            $catPrefix = $category ? strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $category['name']) ?: 'DSH', 0, 3)) : 'DSH';
            $nextNum = $this->itemModel->where('category_id', $categoryId)->countAllResults() + 1;
            $code = sprintf('%s-%02d', $catPrefix, $nextNum);
        }

        $data = [
            'restaurant_id'    => $this->currentRestaurantId ?: 1,
            'branch_id'        => $this->currentBranchId ?: 1,
            'category_id'      => $categoryId,
            'name'             => $name,
            'code'             => $code,
            'description'      => $desc ?: null,
            'price'            => $price,
            'tax_rate_id'      => $taxRateId,
            'is_veg'           => $isVeg,
            'preparation_time' => $prepTime,
            'is_available'     => $isAvail,
            'sort_order'       => $sortOrder,
            'is_active'        => 1,
        ];

        $newId = $this->itemModel->insert($data);

        $this->itemModel->writeAudit('menu_items', 'create', 'menu_items', (int) $newId, null, [
            'name' => $name, 'code' => $code, 'price' => $price, 'category_id' => $categoryId
        ], "Created dish {$name} ({$code})");

        if ($this->request->isAJAX()) {
            return $this->jsonSuccess(['id' => $newId], "Dish '{$name}' added to menu successfully.");
        }

        return redirect()->to(site_url('admin/menu-items'))->with('success', "Dish '{$name}' created successfully.");
    }

    /**
     * Update existing menu item / dish.
     */
    public function updateItem(int $id): ResponseInterface
    {
        if (!$this->can('menu_items.edit') && $this->currentRoleSlug !== 'admin' && $this->currentRoleSlug !== 'manager') {
            $this->authorize('menu_items.edit');
        }

        $item = $this->itemModel->find($id);
        if (!$item) {
            if ($this->request->isAJAX()) {
                return $this->jsonError('Dish not found.', 404);
            }
            return redirect()->back()->with('error', 'Dish not found.');
        }

        $rules = [
            'name'        => 'required|min_length[2]|max_length[150]',
            'category_id' => 'required|is_natural_no_zero',
            'price'       => 'required|numeric',
        ];

        if (!$this->validate($rules)) {
            if ($this->request->isAJAX()) {
                return $this->jsonError('Validation failed', 422, $this->validator->getErrors());
            }
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $name       = trim((string) $this->request->getPost('name'));
        $categoryId = (int) $this->request->getPost('category_id');
        $code       = trim((string) $this->request->getPost('code')) ?: $item['code'];
        $price      = (float) $this->request->getPost('price');
        $desc       = trim((string) $this->request->getPost('description'));
        $taxRateId  = $this->request->getPost('tax_rate_id') ? (int) $this->request->getPost('tax_rate_id') : null;
        $isVeg      = (int) ($this->request->getPost('is_veg') ?? $item['is_veg']);
        $prepTime   = (int) ($this->request->getPost('preparation_time') ?: $item['preparation_time']);
        $sortOrder  = (int) ($this->request->getPost('sort_order') ?? $item['sort_order']);
        $isAvail    = (int) ($this->request->getPost('is_available') ?? $item['is_available']);

        $data = [
            'category_id'      => $categoryId,
            'name'             => $name,
            'code'             => $code,
            'description'      => $desc ?: null,
            'price'            => $price,
            'tax_rate_id'      => $taxRateId,
            'is_veg'           => $isVeg,
            'preparation_time' => $prepTime,
            'is_available'     => $isAvail,
            'sort_order'       => $sortOrder,
        ];

        $this->itemModel->update($id, $data);
        $this->itemModel->writeAudit('menu_items', 'update', 'menu_items', $id, $item, $data, "Updated dish {$name}");

        if ($this->request->isAJAX()) {
            return $this->jsonSuccess(['id' => $id], "Dish '{$name}' updated successfully.");
        }

        return redirect()->to(site_url('admin/menu-items'))->with('success', "Dish '{$name}' updated successfully.");
    }

    /**
     * Delete / deactivate menu item.
     */
    public function deleteItem(int $id): ResponseInterface
    {
        if (!$this->can('menu_items.delete') && $this->currentRoleSlug !== 'admin' && $this->currentRoleSlug !== 'manager') {
            $this->authorize('menu_items.delete');
        }

        $item = $this->itemModel->find($id);
        if (!$item) {
            if ($this->request->isAJAX()) {
                return $this->jsonError('Dish not found.', 404);
            }
            return redirect()->back()->with('error', 'Dish not found.');
        }

        $this->itemModel->update($id, ['is_active' => 0]);
        $this->itemModel->writeAudit('menu_items', 'deactivate', 'menu_items', $id, null, null, "Deactivated dish {$item['name']}");

        if ($this->request->isAJAX()) {
            return $this->jsonSuccess(['id' => $id], "Dish '{$item['name']}' removed from catalog.");
        }

        return redirect()->to(site_url('admin/menu-items'))->with('success', "Dish '{$item['name']}' removed.");
    }

    /**
     * Rapid 86 toggle: mark item in-stock or out-of-stock.
     */
    public function toggleAvailability(int $id): ResponseInterface
    {
        $this->authorize('menu_items.edit');

        $item = $this->itemModel->find($id);
        if (!$item) {
            return $this->jsonError('Item not found.');
        }

        $newVal = (int) $item['is_available'] === 1 ? 0 : 1;
        $this->itemModel->update($id, ['is_available' => $newVal]);

        $statusStr = $newVal === 1 ? 'Available' : 'Out of Stock (86ed)';
        $this->itemModel->writeAudit('menu_items', 'toggle_availability', 'menu_items', $id, [
            'is_available' => $item['is_available']
        ], [
            'is_available' => $newVal
        ], "Item {$item['name']} marked as {$statusStr}");

        if ($this->request->isAJAX()) {
            return $this->jsonSuccess(['is_available' => $newVal], "Item {$item['name']} is now {$statusStr}.");
        }

        return redirect()->back()->with('success', "Item {$item['name']} status updated.");
    }

    /**
     * Store new menu category.
     */
    public function storeCategory(): ResponseInterface
    {
        $this->authorize('menu_categories.create');

        $rules = [
            'name' => 'required|min_length[2]|max_length[100]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $name = (string) $this->request->getPost('name');
        $slug = (string) ($this->request->getPost('slug') ?: preg_replace('/[^a-z0-9]+/i', '-', strtolower($name)));
        $icon = (string) ($this->request->getPost('icon') ?: '🍽️');
        $desc = (string) $this->request->getPost('description');
        $sort = (int) ($this->request->getPost('sort_order') ?? 0);

        $newId = $this->categoryModel->insert([
            'restaurant_id' => $this->currentRestaurantId ?: 1,
            'branch_id'     => $this->currentBranchId ?: 1,
            'name'          => $name,
            'slug'          => $slug,
            'icon'          => $icon,
            'description'   => $desc,
            'sort_order'    => $sort,
            'is_active'     => 1,
        ]);

        $this->categoryModel->writeAudit('menu_categories', 'create', 'menu_categories', (int) $newId, null, [
            'name' => $name, 'slug' => $slug
        ], "Created category {$name}");

        return redirect()->route('admin.menu.categories')->with('success', "Category '{$name}' created successfully.");
    }

    /**
     * Update existing menu category.
     */
    public function updateCategory(int $id): ResponseInterface
    {
        $this->authorize('menu_categories.edit');

        $cat = $this->categoryModel->find($id);
        if (!$cat) {
            return redirect()->back()->with('error', 'Category not found.');
        }

        $rules = [
            'name' => 'required|min_length[2]|max_length[100]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $name = (string) $this->request->getPost('name');
        $slug = (string) ($this->request->getPost('slug') ?: $cat['slug']);
        $icon = (string) ($this->request->getPost('icon') ?: $cat['icon']);
        $desc = (string) $this->request->getPost('description');
        $sort = (int) ($this->request->getPost('sort_order') ?? $cat['sort_order']);

        $data = [
            'name'        => $name,
            'slug'        => $slug,
            'icon'        => $icon,
            'description' => $desc,
            'sort_order'  => $sort,
        ];

        $this->categoryModel->update($id, $data);
        $this->categoryModel->writeAudit('menu_categories', 'update', 'menu_categories', $id, $cat, $data, "Updated category {$name}");

        return redirect()->route('admin.menu.categories')->with('success', "Category '{$name}' updated.");
    }

    /**
     * Delete / deactivate menu category.
     */
    public function deleteCategory(int $id): ResponseInterface
    {
        $this->authorize('menu_categories.delete');

        $cat = $this->categoryModel->find($id);
        if (!$cat) {
            return redirect()->back()->with('error', 'Category not found.');
        }

        $this->categoryModel->update($id, ['is_active' => 0]);
        $this->categoryModel->writeAudit('menu_categories', 'deactivate', 'menu_categories', $id, null, null, "Deactivated category {$cat['name']}");

        return redirect()->route('admin.menu.categories')->with('success', "Category '{$cat['name']}' deactivated.");
    }
}
