<script src="/js/download-result.js"></script>

<div class="row h-100">
    <div class="col-12 align-self-center">
        <div class="w-50 mx-auto">
            <!--
            <form method="POST" action="index.php?action=parse">
            -->
            <form method="POST" name="download">
                <fieldset>
                    <div class="form-group">
                        <label for="link">Ссылка на тред</label>
                        <input type="text" class="form-control" name="link" aria-describedby="input2chLink" placeholder="Enter 2ch thred link">
                    </div>
                    <button type="submit" class="btn btn-primary" name="submit">Загрузить</button>
                </fieldset>
            </form>

            <div id="download-result-string" class="mt-3"></div>
            <div id="execution-status" class="mt-3"></div>
            <div id="download-result" class="mt-3"></div>
        </div>
    </div>
</div>
