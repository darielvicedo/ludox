import {Controller} from '@hotwired/stimulus';

export default class extends Controller {
    static targets = [
        'dailyTicketsList',
    ];

    static values = {
        fetchClientByCi: String,
        newTicketUrl: String,
        loadDailyTicketsUrl: String,
    };

    async connect() {
        await this.loadDailyTickets();
    }

    async loadDailyTickets() {
        fetch(this.loadDailyTicketsUrlValue, {
            method: 'get',
        })
            .then((response) => {
                if (!response.ok) {
                    return response.text().then(text => {
                        throw new Error(text);
                    });
                }

                return response.text();
            })
            .then((view) => {
                this.dailyTicketsListTarget.innerHTML = view;
            })
            .catch((reason) => {
                console.error(reason);
            });
    }

    onCiChange(event) {
        event.preventDefault();

        const input = event.target;
        const value = input.value;
        const query = new URLSearchParams();
        query.append('ci', value);
        const url = this.fetchClientByCiValue + '?' + query.toString();

        if (value.length === 11) {
            fetch(url, {
                method: 'get',
            })
                .then((response) => {
                    if (!response.ok) {
                        return response.text().then(text => {
                            throw new Error(text);
                        });
                    }

                    return response.json();
                })
                .then((data) => {
                    const nameInput = document.getElementById('ticketName');
                    nameInput.value = data.name;
                })
                .catch((reason) => {
                    console.error(reason);
                });
        }
    }

    /**
     * Submit a new daily pass.
     *
     * @param event
     * @returns {Promise<void>}
     */
    async submitTicket(event) {
        event.preventDefault();

        const form = event.target;
        const data = new FormData(form);

        fetch(this.newTicketUrlValue, {
            method: 'post',
            body: data,
        })
            .then((response) => {
                if (!response.ok) {
                    return response.text().then(text => {
                        throw new Error(text);
                    });
                }
            })
            .then(async () => {
                form.reset();
                await this.loadDailyTickets();
            })
            .catch((reason) => {
                console.error(reason);
            });
    }
}
