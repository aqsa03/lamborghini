const UppyVideo = ({
    target,
    maxFileSize,
    tusToken,
    tusEndpoint
}) => {
    const documentWidth = document.body.offsetWidth;
    const uppy = Uppy.Core({
        allowMultipleUploads: false,
        restrictions: {
            maxFileSize: maxFileSize || 5000000000,
            maxNumberOfFiles: 1,
            minNumberOfFiles: null,
            allowedFileTypes: ['.avi', '.mpg', '.mp4', '.flv', '.mov', '.wmv', '.aac', '.ac3', '.mp3', '.wav', '.ogg', '.aiff', '.m4a', '.wma']
        },
    }).use(Uppy.Dashboard, {
        inline: true,
        proudlyDisplayPoweredByUppy: false,
        width: documentWidth <= 500 ? 300 : 580,
        height: documentWidth <= 500 ? 150 : 300,
        showLinkToFileUploadResult: true,
        showProgressDetails: true,
        hideUploadButton: true,
        target: target
    }).use(Uppy.Tus, {
        endpoint: tusEndpoint,
        resume: true,
        autoRetry: true,
        removeFingerprintOnSuccess: true,
        limit: 1,
        retryDelays: [0, 1000, 3000, 5000],
        chunkSize: 60 * 1024 * 1024, // keep it for large files
        headers: {
            'AuthType': 'user',
            'Authorization': `Bearer ${tusToken}`
        }
    });
    return uppy;
};

export default UppyVideo;
