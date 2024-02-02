<div class="list">
    <section>
        <div class="message">
            <?php if (!empty($params['before'])) {
                switch ($params['before']) {
                    case 'created':
                        echo 'Utworzono nową notatkę';
                        break;
                    case 'edited':
                        echo 'Notatka została zaktualizowana';
                        break;
                    case 'deleted':
                        echo 'Notatka została usunięta';
                        break;
                }
            }
            ?>
        </div>
        <div class="message">
            <?php if (!empty($params['error'])) {
                switch ($params['error']) {
                    case 'noteNotFound':
                        echo 'Notatka nie została znaleziona';
                        break;
                    case 'missingNoteId':
                        echo 'Niepoprawny identyfikator notatki';
                        break;
                }
            }

            ?>
        </div>

        <?php
        dump($params['sort']);
        $sort = $params['sort'] ?? [];
        $by = $sort['by'] ?? 'title';
        $order = $sort['order'] ?? 'desc';
        ?>

        <div>
            <form class="settings-form" action="/" method="GET">
                <div>
                    <div>Sortuj po:</div>
                    <label>Tytule: <input name="sortby" type="radio" value="title"></label>
                    <label>Dacie: <input name="sortby" type="radio" value="title"></label>
                </div>
                <div>
                    <div>Kierunek sortowania:</div>
                    <label>Rosnąco: <input name="sortorder" type="radio" value="asc"></label>
                    <label>Malejąco: <input name="sortorder" type="radio" value="desc"></label>
                </div>
                <input type="submit" value="Wyślij">
            </form>
        </div>

        <div class="tbl-header">
            <table cellpadding="0" cellspacing="0" border="0">
                <thead>
                    <tr>
                        <th>Id</th>
                        <th>Tytuł</th>
                        <th>Data</th>
                        <th>Opcje</th>
                    </tr>
                </thead>
            </table>
        </div>
        <div class="tbl-content">
            <table cellpadding="0" cellspacing="0" border="0">
                <tbody>
                    <?php foreach ($params['notes'] ?? [] as $note) : ?>
                        <tr>
                            <td><?php echo $note['id'] ?></td>
                            <td><?php echo $note['title'] ?></td>
                            <td><?php echo $note['created'] ?></td>
                            <td>
                                <a href="/?action=show&id=<?php echo (int) $note['id'] ?>"><button>Więcej</button></a>
                                <a href="/?action=delete&id=<?php echo (int) $note['id'] ?>"><button>Usuń</button></a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>
</div>