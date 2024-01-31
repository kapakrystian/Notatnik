<div>
    <h3>Edytuj notatkę</h3>
    <div>
        <?php if (!empty($params['note'])) : ?>
            <?php $note = $params['note'] ?>
            <form class="note-form" action="/?action=edit" method="post">
                <input name="id" type="hidden" value="<?php echo $note['id'] ?>">
                <ul>
                    <li>
                        <label for="title">Tytuł <span class="required">*</span></label>
                        <input type="text" name="title" class="field-long" value="<?php echo $note['title'] ?>">
                    </li>
                    <li>
                        <label for="description">Treść <span class="required">*</span></label>
                        <textarea name="description" id="field5" class="field-long field-textarea"><?php echo $note['description'] ?></textarea>
                    </li>
                    <li>
                        <input type="submit" value="Submit">
                    </li>
                </ul>
            </form>
        <?php else : ?>
            <div>
                Brak danych do wyświetlenia
                <a href="/"><button>Powrót do listy notatek</button></a>
            </div>
        <?php endif; ?>
    </div>
</div>