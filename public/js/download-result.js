$(document).ready(function ()
{
    document.forms.download.onsubmit = function () {
        $(this.submit).attr("disabled", ""); // Удалить атрибут .removeAttr("title")	
        $(this.submit).text("Загрузка...");
        AjaxFormRequest("download-result", this, "downloadAjaxHandler.php", this.submit);
        document.getElementById("download-result").innerHTML = "<p>Идёт загрузка...</p>";
        //var checkInterval = setInterval(AjaxCheckingDownloadStatus(checkInterval, "download-result", "heckingAjaxDownloadStatus.php", this.submit), 1000);
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
            data: $(formObj).serialize(),
            success: function(response) {
                document.getElementById(resultID).innerHTML = document.getElementById(resultID).innerHTML + response;
                $(submit).removeAttr("disabled"); // делает кнопку отправки формы снова активной
                $(submit).text("Загрузить");
            },
            error: function(response) {
                document.getElementById(resultID).innerHTML = "Ошибка при отправке формы.";
                $(submit).removeAttr("disabled"); // делает кнопку отправки формы снова активной
                $(submit).text("Загрузить");
            }
        });
    }
    
    /**
     * Проверяет статус выполнения загрузки файлов на сервер
     */
    function AjaxCheckingDownloadStatus(timerId, resultID, url, submit) {
        $.ajax({
            url: url,
            type: "POST",
            dataType: "html",
            data: $(formObj).serialize(),
            success: function(response) {
                document.getElementById(resultID).innerHTML = document.getElementById(resultID).innerHTML + response;
                clearInterval(timerId);
                $(submit).removeAttr("disabled"); // делает кнопку отправки формы снова активной
                $(submit).text("Загрузить");
            },
            error: function(response) {
                clearInterval(timerId);
                document.getElementById(resultID).innerHTML = "Ошибка при отправке формы." + response;
                $(submit).removeAttr("disabled"); // делает кнопку отправки формы снова активной
                $(submit).text("Загрузить");
            }
        });
    }
}
)

