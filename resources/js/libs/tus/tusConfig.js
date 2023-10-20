export default class TusConfig {
    /**
     * @param {Object} params
     * @param {String} params.tusEndpoint the path/URL to the TUS upload
     * @param {String} params.tusToken the TUS upload token
     */
     constructor({ tusEndpoint, tusToken }) {
        this.tusEndpoint = tusEndpoint;
        this.tusToken = tusToken;
    }
}
