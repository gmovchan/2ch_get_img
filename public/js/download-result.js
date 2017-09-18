$(document).ready(function ()
{
    document.forms.download.onsubmit = function () {
        AjaxFormRequest("download-result", this, "downloadAjaxHandler.php", this.submit);

        // Ожидает ответ скрипта по завершению его выполнения.
        //document.getElementById("download-result").innerHTML = "<p>Идёт загрузка...</p>";

        //AjaxCheckingDownloadStatusTest("execution-status", "checkingAjaxDownloadStatus.php")

        var checkInterval = setInterval(function () {
            AjaxCheckingDownloadStatus("execution-status", "download-result-string", "checkingAjaxDownloadStatus.php", checkInterval);
        }, 100);

        return false;
    }

    /**
     * Отправляет содержимое формы на сервер
     */
    function AjaxFormRequest(resultID, formObj, url, submit) {
        formFieldset = $(formObj).find('fieldset');
        formData = $(formObj).serialize(); // Жквери не сериализует формы в атрибутом desabled

        formFieldset.attr("disabled", "");
        $(formObj.submit).text("Загрузка...");

        $.ajax({
            url: url,
            type: "POST",
            dataType: "html",
            timeout: 600000,
            data: formData,
            success: function (response) {
                //document.getElementById(resultID).innerHTML = document.getElementById(resultID).innerHTML + response;
                formFieldset.removeAttr("disabled"); // делает форму снова активной
                $(submit).text("Загрузить");
            },
            error: function (response) {
                //document.getElementById(resultID).innerHTML = "Ошибка при отправке формы.";
                formFieldset.removeAttr("disabled"); // делает форму снова активной
                $(submit).text("Загрузить");
            }
        });
    }

    /**
     * Проверяет статус выполнения загрузки файлов на сервер
     */
    function AjaxCheckingDownloadStatus(resultID, resultIDStrin, url, timerId) {
        $.ajax({
            url: url,
            type: "POST",
            dataType: "html",
            data: '',
            success: function (response) {
                responseJSON = JSON.parse(response);

                console.log("Интервал работает.");
                console.log(responseJSON);
                console.log(timerId);

                document.getElementById(resultID).innerHTML = "Скачано: " + responseJSON.statusBar;
                document.getElementById(resultIDStrin).innerHTML = "Выполняется: " + responseJSON.statusText;

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

