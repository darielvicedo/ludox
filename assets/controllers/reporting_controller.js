import {Controller} from '@hotwired/stimulus';

import {DateTime} from "luxon";

export default class extends Controller {
    static targets = [
        'monthSelect',
        'view',
    ];

    static values = {
        loadReportUrl: String,
    };

    showDate = DateTime.now();

    connect() {
        // select current month and year
        this.monthSelectTarget.value = this.showDate.toFormat('yyyy-LL');

        this.loadReport();
    }

    subMonth(event) {
        event.preventDefault();

        this.showDate = this.showDate.minus({months: 1});
        this.monthSelectTarget.value = this.showDate.toFormat('yyyy-LL');

        this.loadReport();
    }

    addMonth(event) {
        event.preventDefault();

        this.showDate = this.showDate.plus({months: 1});
        this.monthSelectTarget.value = this.showDate.toFormat('yyyy-LL');

        this.loadReport();
    }

    loadReport() {
        const body = {
            month: this.showDate.month,
            year: this.showDate.year,
        };

        fetch(`${this.loadReportUrlValue}`, {
            method: 'post',
            body: JSON.stringify(body),
        })
            .then(response => {
                if (!response.ok) {
                    return response.text().then(text => {
                        throw new Error(text);
                    });
                }

                return response.text();
            })
            .then((result) => {
                this.viewTarget.innerHTML = result;
            })
            .catch(reason => {
                console.error(reason);
            });
    }
}
