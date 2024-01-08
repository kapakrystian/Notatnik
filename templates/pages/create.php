<div>
    <h3>Nowa notatka</h3>
    <div>
        <?php if ($params['created']) : ?>
            <div>
                <div><?php echo "Tytuł: " . $params['title'] ?></div>
                <div><?php echo "Treść: " . $params['description'] ?></div>
            </div>

        <?php else : ?>
            <form class="note-form" action="/?action=create" method="post">
                <ul>
                    <li>
                        <label for="title">Tytuł <span class="required">*</span></label>
                        <input type="text" name="title" class="field-long">
                    </li>
                    <li>
                        <label for="description">Treść <span class="required">*</span></label>
                        <textarea name="description" id="field5" class="field-long field-textarea"></textarea>
                    </li>
                    <li>
                        <input type="submit" value="Submit">
                    </li>
                </ul>
            </form>
        <?php endif; ?>
    </div>
</div>