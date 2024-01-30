<div class="show">
    <?php $note = $params['note'] ?? null; ?>
    <?php if ($note) : ?>
        <ul>
            <li>ID: <?php echo $note['id'] ?></li>
            <li>Tytuł: <?php $note['title'] ?></li>
            <li>Szczegóły: <?php echo $note['description'] ?></li>
            <li>Zapisano: <?php echo $note['created'] ?></li>
        </ul>
        <a href="/?action=edit&id=<?php echo $note['id'] ?>">
            <button>Edytuj</button></a>
    <?php else : ?>
        <div>
            Brak notatki do wyświetlenia
        </div>
    <?php endif; ?>
    <a href="/"><button>Powrót do listy notatek</button></a>
</div>