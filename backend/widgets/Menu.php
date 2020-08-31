<?php

namespace backend\widgets;

use Yii;

class Menu extends \yiister\gentelella\widgets\Menu
{
    public $activateInController;

    protected function isItemActive($item)
    {
        if (isset($item['url']) && is_array($item['url']) && isset($item['url'][0])) {
            $route = Yii::getAlias($item['url'][0]);
            if ($route[0] !== '/' && Yii::$app->controller) {
                $route = Yii::$app->controller->module->getUniqueId() . '/' . $route;
            }

            // If routes in the same controller
            if ($this->activateInController === true) {
                $routeArr = explode('/', ltrim($route, '/'));
                $thisRouteArr = explode('/', $this->route);
                array_pop($routeArr);
                array_pop($thisRouteArr);
                if (implode('/', $routeArr) === implode('/', $thisRouteArr)) {
                    return true;
                }
            }

            if (ltrim($route, '/') !== $this->route) {
                return false;
            }
            unset($item['url']['#']);
            if (count($item['url']) > 1) {
                $params = $item['url'];
                unset($params[0]);
                foreach ($params as $name => $value) {
                    if ($value !== null && (!isset($this->params[$name]) || $this->params[$name] != $value)) {
                        return false;
                    }
                }
            }

            return true;
        }

        return false;
    }
}
