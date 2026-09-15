<?php

namespace App\Menu\Filters;

use ColorlibHQ\AdminLte\Menu\Filters\FilterInterface;
use Illuminate\Support\Facades\Gate;

class RecursiveGateFilter implements FilterInterface
{
    /**
     * Filter menu dan seluruh submenu berdasarkan permission.
     */
    public function transform(array $item): ?array
    {
        // Filter submenu terlebih dahulu.
        if (isset($item['submenu']) && is_array($item['submenu'])) {

            $filteredSubmenu = [];

            foreach ($item['submenu'] as $child) {

                $filteredChild = $this->transform($child);

                if ($filteredChild !== null) {
                    $filteredSubmenu[] = $filteredChild;
                }
            }

            $item['submenu'] = $filteredSubmenu;

            // Jika parent tidak punya can tetapi semua submenu hilang,
            // parent juga tidak perlu ditampilkan.
            if (! isset($item['can']) && empty($item['submenu'])) {
                return null;
            }
        }

        // Jika item mempunyai permission, cek permission tersebut.
        if (isset($item['can'])) {

            $abilities = (array) $item['can'];
            $params = $item['can_params'] ?? [];

            foreach ($abilities as $ability) {

                if (Gate::allows($ability, $params)) {
                    return $item;
                }
            }

            return null;
        }

        return $item;
    }
}
