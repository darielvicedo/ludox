import {Controller} from '@hotwired/stimulus';

export default class extends Controller {
    static targets = [
        'selectForm',
        'assetsTable',
    ];

    static values = {
        loadAssetsForm: String,
        addAssetUrl: String,
        loadAssetsTableUrl: String,
    };

    async connect() {
        await this.loadAssetsForm();
        await this.loadAssetsTable();
    }

    /**
     * Loads the assets select form.
     *
     * @returns {Promise<void>}
     */
    async loadAssetsForm() {
        fetch(this.loadAssetsFormValue, {
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
                this.selectFormTarget.innerHTML = view;
            })
            .catch((reason) => {
                console.error(reason);
            });
    }

    /**
     * Loads assets table.
     *
     * @returns {Promise<void>}
     */
    async loadAssetsTable() {
        fetch(this.loadAssetsTableUrlValue, {
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
                this.assetsTableTarget.innerHTML = view;
            })
            .catch((reason) => {
                console.error(reason);
            });
    }

    /**
     * Add asset to the game.
     *
     * @param event
     * @returns {Promise<void>}
     */
    async addAsset(event) {
        event.preventDefault();

        const form = event.target;
        const data = new FormData(form);

        fetch(this.addAssetUrlValue, {
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
                await this.loadAssetsForm();
                await this.loadAssetsTable();
            })
            .catch((reason) => {
                console.error(reason);
            });
    }
}
