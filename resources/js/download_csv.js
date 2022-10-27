window.addEventListener('DOMContentLoaded', function () {
    new DownloadCsvController();
})

class DownloadCsvController {
    constructor() {
        this.btnBusinessHourCsv = document.querySelector('#btn-businesshour');
        this.btnBusinessHourCsv.addEventListener('click', this.downloadBusinessHourCsv);

        this.btnVacationCsv = document.querySelector('#btn-vacation');
        this.btnVacationCsv.addEventListener('click', this.downloadVacationCsv);
    }

    // 診療時間csvダウンロード
    downloadBusinessHourCsv() {
        location.href = `/download/business_hour/${document.querySelector('select[name="hospital-businesshour"]').value}`;
    }

    // 長期休暇csvダウンロード
    downloadVacationCsv() {

        location.href = `/download/vacation/${document.querySelector('select[name="hospital-vacation"]').value}`;
    }

}
