import {Controller} from '@hotwired/stimulus';

import Swal from "sweetalert2";

export default class extends Controller {
    static targets = [
        'dt',
        'filterForm',
        'newForm',
    ];

    static values = {
        loadAssetsUrl: String,
        newAssetFormUrl: String,
        assetToEntryUrl: String,
    };

    page = 1;
    all = false;

    connect() {
        this.loadNewForm();
        this.loadTable();
    }

    /**
     * Paginate the dynamic table.
     *
     * @param event
     */
    onPaginate(event) {
        event.preventDefault();

        this.page = event.currentTarget.dataset.page;
        this.loadTable();
    }

    /**
     * Load assets dynamic table.
     */
    loadTable() {
        const data = new FormData(this.filterFormTarget);
        data.append('filter_page', this.page);

        fetch(this.loadAssetsUrlValue, {
            method: 'post',
            body: data,
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
                this.dtTarget.innerHTML = view;
            })
            .catch((reason) => {
                console.error(reason);
            });
    }

    /**
     * Loads new asset form.
     */
    loadNewForm() {
        fetch(this.newAssetFormUrlValue, {
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
                this.newFormTarget.innerHTML = view;
            })
            .catch((reason) => {
                console.error(reason);
            });
    }

    /**
     * Posts new asset.
     *
     * @param event
     */
    new(event) {
        event.preventDefault();

        const button = event.submitter;
        button.disabled = true;

        const form = event.target;
        const data = new FormData(form);

        fetch(this.newAssetFormUrlValue, {
            method: 'post',
            body: data,
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
                this.newFormTarget.innerHTML = view;
                this.loadTable();
            })
            .catch((reason) => {
                console.error(reason);
            });
    }

    /**
     * Select all visible rows.
     */
    selectAll() {
        const checks = document.getElementsByClassName('dt-row-select');

        for (const check of checks) {
            check.checked = !this.all;
        }

        this.all = !this.all;
    }

    /**
     * Unselect all visible rows.
     */
    unselectAll() {
        const checks = document.getElementsByClassName('dt-row-select');

        for (const check of checks) {
            check.checked = false;
        }
    }

    /**
     * Creates account entry from assets.
     *
     * @param event
     */
    toEntry(event) {
        event.preventDefault();

        // lock select
        const select = event.currentTarget;
        select.disabled = true;

        // get account id
        const accountId = select.value;

        // get selected rows
        const assets = [];
        const checks = document.getElementsByClassName('dt-row-select');
        for (const check of checks) {
            if (check.checked) {
                assets.push(check.dataset.assetId);
            }
        }

        // confirm
        if (window.confirm("Creates account entries from " + assets.length + " selected elements?")) {
            fetch(this.assetToEntryUrlValue, {
                method: 'post',
                body: JSON.stringify({
                    accountId: accountId,
                    assets: assets,
                }),
            })
                .then((response) => {
                    if (!response.ok) {
                        return response.text().then(text => {
                            throw new Error(text);
                        });
                    }
                })
                .then(() => {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 5000,
                        icon: 'success',
                        title: 'OK',
                        text: 'Los asientos contables fueron creados.'
                    });
                })
                .catch((reason) => {
                    console.error(reason);
                })
                .finally(() => {
                    this.unselectAll();
                    select.selectedIndex = 0;
                    select.disabled = false;
                });
        }
    }
}
