export default class TusVideo {

    static extractURL(url) {
        return url.replace("uploads/files", "file")
    }
}
