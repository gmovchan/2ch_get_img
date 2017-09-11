<div class="row h-100">
    <div class="col-12 align-self-center result">
        <div class="w-50 mx-auto">
            <p>
                <?php
                if (!empty($data['result'])) {
                    echo "Результат:";
                }
                ?>
            </p>
            <p>
                <?php
                if (isset($data['result']['errors'])) {
                    if (!empty($data['result']['errors'])) {
                        echo '<p>Ошибки:</p>';
                        foreach ($data['result']['errors'] as $error) {
                            echo "<p>$error</p>";
                        }
                    }
                }

                if (isset($data['result']['success'])) {
                    if (!empty($data['result']['success'])) {
                        echo '<p>Успех:</p>';
                        foreach ($data['result']['success'] as $success) {
                            echo "<p>$success</p>";
                        }
                    }
                }
                ?>
            </p>            
        </div>
    </div>
</div>
