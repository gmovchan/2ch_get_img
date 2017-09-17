$(document).ready(function ()
{
    document.forms.download.onsubmit = function () {
        $(this.submit).attr("disabled", ""); // Удалить атрибут .removeAttr("title")	
        $(this.submit).text("Загрузка...");
        AjaxFormRequest("download-result", this, "downloadAjaxHandler.php", this.submit);

        // Ожидает ответ скрипта по завершению его выполнения.
        document.getElementById("download-result").innerHTML = "<p>Идёт загрузка...</p>";
        AjaxCheckingDownloadStatusTest("execution-status", "checkingAjaxDownloadStatus.php")

        var checkInterval = setInterval(function () {
            AjaxCheckingDownloadStatus("execution-status", "checkingAjaxDownloadStatus.php", checkInterval);
        }, 100);

        return false;
    }

    /**
     * Отправляет содержимое формы на сервер
     */
    function AjaxFormRequest(resultID, formObj, url, submit) {
        $.ajax({
            url: url,
            type: "POST",
            dataType: "html",
            timeout: 600000,
            data: $(formObj).serialize(),
            success: function (response) {
                document.getElementById(resultID).innerHTML = document.getElementById(resultID).innerHTML + response;
                $(submit).removeAttr("disabled"); // делает кнопку отправки формы снова активной
                $(submit).text("Загрузить");
            },
            error: function (response) {
                document.getElementById(resultID).innerHTML = "Ошибка при отправке формы.";
                $(submit).removeAttr("disabled"); // делает кнопку отправки формы снова активной
                $(submit).text("Загрузить");
            }
        });
    }

    /**
     * Проверяет статус выполнения загрузки файлов на сервер
     */
    function AjaxCheckingDownloadStatus(resultID, url, timerId) {
        $.ajax({
            url: url,
            type: "POST",
            dataType: "html",
            data: '',
            success: function (response) {
                responseJSON = JSON.parse(response);
                /*
                console.log("Интервал работает.");
                console.log(responseJSON);
                console.log(timerId);
                */
               
                document.getElementById(resultID).innerHTML = "Скачано: " + responseJSON.statusBar;
                
                if (responseJSON.downloadingComplete === true) {
                    clearInterval(timerId);
                }
            },
            error: function (response) {
                document.getElementById(resultID).innerHTML = "Не удалось получить статус выполнения.";
                clearInterval(timerId);
            }
        });
    }

    function AjaxCheckingDownloadStatusTest(resultID, url) {
        $.ajax({
            url: url,
            type: "POST",
            dataType: "html",
            data: true,
            success: function (response) {
                document.getElementById(resultID).innerHTML = "Скачано: " + response;
            },
            error: function (response) {
                document.getElementById(resultID).innerHTML = "Не удалось получить статус выполнения.";
            }
        });
    }
}
)

