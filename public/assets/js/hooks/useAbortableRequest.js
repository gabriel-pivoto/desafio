export function useAbortableRequest() {
    let controller = null;

    function abort() {
        controller?.abort();
        controller = null;
    }

    async function run(task) {
        controller?.abort();
        controller = new AbortController();

        const current = controller;

        try {
            return await task(current.signal);
        } finally {
            if (controller === current) {
                controller = null;
            }
        }
    }

    function aborted(signal) {
        return signal.aborted;
    }

    return { run, abort, aborted };
}
