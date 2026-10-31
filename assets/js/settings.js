/* Keep conditional settings accessible when JavaScript is unavailable. */
(() => {
    const update = () => {
        document.querySelectorAll('[data-stl-conditions]').forEach((row) => {
            const rules = JSON.parse(row.dataset.stlConditions);
            row.hidden = !rules.every((rule) => {
                const input = document.getElementById(`stl-${rule.field}`);
                return !input || rule.operator !== '=' || input.value === rule.value;
            });
        });
    };
    document.addEventListener('change', update);
    update();
})();
