<div>
    <div class="message">
        <?php if (!empty($params['before'])) {
            switch ($params['before']) {
                case 'created':
                    echo 'Utworzono nową notatkę';
                    break;
            }
        }
        ?>
    </div>
    <h3>Lista notatek</h3>
    <b>
        <?php
        echo $params['resultList'] ?? '';
        ?>
    </b>
</div>