import { ref, watch, onUnmounted } from 'vue';

export function useTypewriter(speed = 25) {
    const source = ref('');
    const displayed = ref('');
    const isTyping = ref(false);

    let frame: ReturnType<typeof requestAnimationFrame> | null = null;
    let index = 0;
    let lastTime = 0;

    function stop() {
        if (frame) {
            cancelAnimationFrame(frame);
            frame = null;
        }
        isTyping.value = false;
    }

    function tick(time: number) {
        if (!source.value) return;

        if (time - lastTime >= speed) {
            // Type multiple chars if tab is backgrounded and catches up
            const charsToAdd = Math.min(
                Math.ceil((time - lastTime) / speed),
                5,
            );

            index = Math.min(index + charsToAdd, source.value.length);
            displayed.value = source.value.slice(0, index);
            lastTime = time;
        }

        if (index < source.value.length) {
            frame = requestAnimationFrame(tick);
        } else {
            isTyping.value = false;
        }
    }

    function start(text: string) {
        stop();
        source.value = text;
        displayed.value = '';
        index = 0;
        lastTime = 0;
        isTyping.value = true;
        frame = requestAnimationFrame(tick);
    }

    function skipToEnd() {
        stop();
        displayed.value = source.value;
    }

    onUnmounted(stop);

    return { displayed, isTyping, start, skipToEnd, stop };
}
