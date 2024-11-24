export default () => {
    const taskPriority = Object.freeze({
        Urgent: 1,
        High: 2,
        Normal: 3,
        Low: 4
    });

    // get status

    const taskIdentity = (status) => {
        if (status == taskPriority.Low) {
            return { status: 'Low' };
        }

        if (status == taskPriority.Normal) {
            return { status: 'Normal' };
        }

        if (status == taskPriority.High) {
            return { status: 'High' };
        }

        if (status == taskPriority.Urgent) {
            return { status: 'Urgent' };
        }

        return { status: 'No selected' };
    }

    return {
        taskPriority,
        taskIdentity,
      };
};
