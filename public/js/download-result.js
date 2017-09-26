$(document).ready(function ()
{
    document.forms.download.onsubmit = function () {
        AjaxFormRequest("download-result", this, "downloadAjaxHandler.php", this.submit);

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

                if (responseJSON.statusBar !== null) {
                    document.getElementById(resultID).innerHTML = "Скачано " + responseJSON.statusBar + " файлов.";

                    if (responseJSON.archiveFileName !== null) {
                        console.log(" <a href=\"/output.php?filename=" + responseJSON.archiveFileName + "\>Скачать архив.</a>");
                        document.getElementById(resultID).innerHTML = document.getElementById(resultID).innerHTML + " <a href=\"/output.php?filename=" + responseJSON.archiveFileName + "\">Скачать архив.</a>";
                    }

                } else {
                    document.getElementById(resultID).innerHTML = "";
                }

                if (responseJSON.statusText !== null) {
                    document.getElementById(resultIDStrin).innerHTML = responseJSON.statusText;
                } else {
                    document.getElementById(resultIDStrin).innerHTML = "";
                }

                /*
                 document.getElementById(resultID).innerHTML = "Скачано " + responseJSON.statusBar + " файлов.";
                 document.getElementById(resultIDStrin).innerHTML = responseJSON.statusText;
                 */

                if (responseJSON.downloadingComplete === true) {
                    clearInterval(timerId);
                }
            },
            error: function (response) {
                document.getElementById(resultID).innerHTML = "Не удалось получить ответ от сервера.";
                clearInterval(timerId);
            }
        });
    }
}
)

