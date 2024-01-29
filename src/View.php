<?php

declare(strict_types=1);

namespace App;

class View
{
    public function render(string $page, array $params = []): void
    {
        $params = $this->escape($params);
        require_once('templates/pages/layout.php');
    }

    private function escape(array $params): array
    {
        $clearParams = [];

        foreach ($params as $key => $param) {
            if (is_array($param)) {
                $clearParams[$key] = $this->escape($param);
            } else if ($param) {
                if (is_int($param)) {
                    $clearParams[$key] = (int) $param;
                } else {
                    $clearParams[$key] = htmlentities($param);
                }
            } else {
                $clearParams[$key] = $param;
            }
        }

        return $clearParams;
    }
}
