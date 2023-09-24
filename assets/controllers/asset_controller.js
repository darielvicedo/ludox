import {Controller} from '@hotwired/stimulus';

export default class extends Controller {
    static targets = [
        'dt',
        'filterForm',
        'newForm',
    ];

    static values = {
        loadAssetsUrl: String,
        newAssetFormUrl: String,
    };

    page = 1;

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
     *
     * @param event
     */
    selectAll(event) {
        event.preventDefault();

        const checks = document.getElementsByClassName('dt-row-select');

        for (const check of checks) {
            check.checked = !check.checked;
        }
    }
}
