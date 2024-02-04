/**
 * SelectPlus
 *
 * Stimulus widget for select & create elements.
 *
 * @copyright LinkToMedia 2022
 * @author Dariel Vicedo <darielvicedo@gmail.com>
 */

import {Controller} from "@hotwired/stimulus";

export default class extends Controller {
    static targets = [
        "inputQuery",
        "inputValue",
        "listResults",
    ];

    static values = {
        fetchUrl: String,
        minQueryLength: {type: Number, default: 3},
        maxQueryResults: {type: Number, default: 3},
        create: {type: Boolean, default: true}
    };

    model = {};

    connect() {
        // query input events
        this.inputQueryTarget.addEventListener("input", this.onInputChange);
        this.inputQueryTarget.addEventListener("focus", this.onInputFocus);

        // lost focus
        document.addEventListener("click", (event) => {
            if (!this.inputQueryTarget.contains(event.target)) {
                this.listResultsTarget.classList.add("visually-hidden");
            }
        });
    }

    /**
     * When user writes the query.
     *
     * @param event
     */
    onInputChange = async (event) => {
        const query = event.target.value.trim();
        this.model = {};

        if (query && query.length >= this.minQueryLengthValue) {
            await this.loadModel(query);
            await this.renderModel();
        } else {
            this.listResultsTarget.classList.add("visually-hidden");
            this.listResultsTarget.innerText = "";
        }
    }

    /**
     * Displays options list.
     */
    onInputFocus = () => {
        this.listResultsTarget.classList.remove("visually-hidden");
    }

    /**
     * When an option is clicked.
     *
     * @param event
     */
    onResultClick = (event) => {
        event.preventDefault();

        if ("button" === event.target.type) {
            const value = parseInt(event.target.getAttribute("value"));
            let text = event.target.dataset.textValue;

            this.inputValueTarget.value = value;
            this.inputQueryTarget.value = text;

            this.listResultsTarget.classList.add("visually-hidden");
        }
    }

    /**
     * Loads data model from result.
     *
     * @param query
     * @returns {Promise<void>}
     */
    loadModel = async (query) => {
        const results = await this.fetchResults(query);

        // can create?
        if (this.createValue) {
            this.model["0"] = {
                label: `Add "${query}"...`,
                value: query,
            };
        }

        results.forEach((entry) => {
            const regex = new RegExp(`${query}`, 'gi');
            const original = entry.name.match(regex);
            const highlighted = entry.name.replace(regex, "<strong>" + original + "</strong>");

            this.model[`${entry.id}`] = {
                label: highlighted,
                value: entry.name,
            };
        });
    }

    /**
     * Renders the model as options.
     *
     * @returns {Promise<void>}
     */
    renderModel = async () => {
        this.listResultsTarget.innerText = "";

        for (const key in this.model) {
            if (this.model.hasOwnProperty(key)) {
                let classes = "";
                if (0 === parseInt(key)) {
                    classes = "text-muted fs-6 fw-bold";
                }

                const button = this.createOptionButton(key, this.model[key].value, this.model[key].label, classes);
                button.addEventListener("click", this.onResultClick);
                this.listResultsTarget.appendChild(button);
            }
        }

        // count results
        let matches = Object.keys(this.model).length;
        if (this.createValue) {
            matches--;
        }

        // create results count element.
        const countDiv = document.createElement("small");
        countDiv.classList.add("list-group-item", "text-end", "p-1", "bg-secondary", "text-white");
        countDiv.innerText = matches + " matches found.";
        this.listResultsTarget.appendChild(countDiv);

        this.listResultsTarget.classList.remove("visually-hidden");
    }

    /**
     * Fetch results based on query.
     *
     * @param query
     * @returns {Promise<any>}
     */
    fetchResults = async (query) => {
        const response = await fetch(this.fetchUrlValue, {
            method: 'POST',
            body: JSON.stringify({
                query: query,
                maxResults: this.maxQueryResultsValue,
            }),
        });

        return await response.json();
    }

    /**
     * Creates option element.
     *
     * @param value
     * @param text
     * @param label
     * @param extraClasses
     * @returns {HTMLAnchorElement}
     */
    createOptionButton(value, text, label, extraClasses = "") {
        const button = document.createElement("a");

        button.classList.value = "list-group-item list-group-item-action" + " " + extraClasses;
        button.setAttribute("type", "button");
        button.setAttribute("href", "#");
        button.setAttribute("value", value);
        button.dataset.textValue = text;
        button.innerHTML = label;

        button.addEventListener("mouseover", function () {
            this.classList.add("bg-lgray");
        });

        button.addEventListener("mouseout", function () {
            this.classList.remove("bg-lgray");
        });

        return button;
    }
}
