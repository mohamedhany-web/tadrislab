<?php

namespace App\Services;

use App\Models\LearningPath;
use App\Models\Order;
use App\Models\Package;
use App\Models\User;
use Illuminate\Support\Facades\Log;

/**
 * Fulfill TADRIS catalog orders (Package / Learning Path) after payment approval.
 */
class CatalogOrderFulfillmentService
{
    public static function fulfill(Order $order, ?User $actor = null): void
    {
        $order->loadMissing(['user', 'package', 'learningPath']);
        $user = $order->user;
        if (! $user) {
            return;
        }

        if ($order->package_id && $order->package) {
            try {
                PackageEntitlementService::activateForUser(
                    $order->package,
                    $user,
                    $order,
                    $actor
                );
            } catch (\Throwable $e) {
                Log::error('Catalog package fulfill failed', [
                    'order_id' => $order->id,
                    'package_id' => $order->package_id,
                    'error' => $e->getMessage(),
                ]);
                throw $e;
            }

            return;
        }

        if ($order->learning_path_id && $order->learningPath) {
            try {
                LearningPathAccessService::activateStandalonePath(
                    $order->learningPath,
                    $user,
                    $order,
                    $actor
                );
            } catch (\Throwable $e) {
                Log::error('Catalog learning path fulfill failed', [
                    'order_id' => $order->id,
                    'learning_path_id' => $order->learning_path_id,
                    'error' => $e->getMessage(),
                ]);
                throw $e;
            }
        }
    }

    public static function isCatalogOrder(Order $order): bool
    {
        return (bool) ($order->package_id || $order->learning_path_id)
            || in_array($order->order_type, [Order::TYPE_PACKAGE, Order::TYPE_LEARNING_PATH], true);
    }
}
