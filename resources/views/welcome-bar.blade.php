@inject('welcomeBarService', 'Daikazu\WelcomeBar\Services\WelcomeBarService')

@php
    //  Get the array of welcome bars (from storage or wherever your service fetches it).
        $bars = $welcomeBarService->getData();

        // Filter bars by "schedule.start" and "schedule.end".
        // We'll assume "start" and "end" are either valid ISO date strings or missing.
        // Only show bars where now >= start AND now <= end
        $now = now();

        $bars = collect($bars)->filter(function($bar) use ($now) {
            // If no schedule object, assume it's always valid
            if (! isset($bar['schedule'])) {
                return true;
            }

            $start = isset($bar['schedule']['start']) ? $bar['schedule']['start'] : null;
            $end   = isset($bar['schedule']['end'])   ? $bar['schedule']['end']   : null;

            // Convert to Carbon if present
            if ($start) {
                $start = \Carbon\Carbon::parse($start);
                if ($now->lt($start)) {
                    return false;
                }
            }
            if ($end) {
                $end = \Carbon\Carbon::parse($end);
                if ($now->gt($end)) {
                    return false;
                }
            }

            return true;
        })->values(); // Re-index the collection
@endphp

@if ($bars->count() > 0)

    <!-- Wrapper that holds *all* stacked bars -->
    <div class="welcome-bar-wrapper" style="position: fixed; top: 0; left: 0; width: 100%; z-index: 9999;">
        @foreach ($bars as $bar)
            @php
                // Grab top-level data
                $message = $bar['message'] ?? '(No message)';

                // CTA
                $cta = $bar['cta'] ?? [];
                $ctaLabel  = $cta['label']  ?? '';
                $ctaUrl    = $cta['url']    ?? '';
                $ctaTarget = $cta['target'] ?? '_self';

                // Behavior
                $behavior = $bar['behavior'] ?? [];
                $closable       = $behavior['closable']       ?? false;
                $autoHide       = $behavior['autoHide']       ?? false;
                $autoHideDelay  = $behavior['autoHideDelay']  ?? 5000; // default 5s if omitted

                // Theme
                $theme = $bar['theme'] ?? [];
                $variant        = $theme['variant']         ?? 'default';
                $bgColor        = $theme['background']      ?? '#000';
                $textColor      = $theme['text']            ?? '#fff';
                $buttonBg       = $theme['button']['background'] ?? '#fff';
                $buttonText     = $theme['button']['text']       ?? '#000';
                $contrast       = $theme['button']['contrastStrategy'] ?? 'manual';

                // If contrastStrategy = 'auto', you might compute a better text color:
                if ($contrast === 'auto') {
                    // For brevity, let's do a quick check (a real function might be more thorough):
                    // Example: Basic YIQ or HSP color check for contrast
                    $hex = str_replace('#', '', $buttonBg);
                    if (strlen($hex) === 6) {
                        $r = hexdec(substr($hex, 0, 2));
                        $g = hexdec(substr($hex, 2, 2));
                        $b = hexdec(substr($hex, 4, 2));
                        $yiq = (($r*299) + ($g*587) + ($b*114)) / 1000;
                        $buttonText = ($yiq >= 128) ? '#000' : '#fff';
                    }
                }

                // Generate a unique ID for auto-hide or close logic
                $barId = 'welcome-bar-' . $loop->index;
            @endphp

            <div
                id="{{ $barId }}"
                class="welcome-bar"
                style="
                    background-color: {{ $bgColor }};
                    color: {{ $textColor }};
                    display: flex;
                    align-items:  center;
                    justify-content: space-between;
                    padding: 0.75rem 1rem;
                    font-weight: bold;
                "
                {{-- We can store data attributes for auto-hide in JS --}}
                data-auto-hide="{{ $autoHide ? 'true' : 'false' }}"
                data-auto-hide-delay="{{ $autoHideDelay }}"
            >
                <span class="welcome-bar-message">
                    {{ $message }}
                </span>

                <div style="
                    display: flex;
                    align-items: center;
                    justify-content: space-between;
                    gap: 1rem;
                ">
                    @if ($ctaLabel && $ctaUrl)
                        <a
                            href="{{ $ctaUrl }}"
                            target="{{ $ctaTarget }}"
                            class="welcome-bar-cta"
                            style="
                            margin-left: 1rem;
                            padding: 0.5rem 1rem;
                            background-color: {{ $buttonBg }};
                            color: {{ $buttonText }};
                            text-decoration: none;
                            border-radius: 3px;
                            font-weight: normal;
                        "
                        >
                            {{ $ctaLabel }}
                        </a>
                    @endif

                    @if ($closable)
                        <button
                            type="button"
                            class="welcome-bar-close"
                            style="
                            margin-left: 1rem;
                            background: transparent;
                            border: none;
                            color: inherit;
                            font-size: 1.25rem;
                            cursor: pointer;
                        "
                        >
                            &times;
                        </button>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    <style>
        .welcome-bar {
            transition: transform 0.3s ease, opacity 0.3s ease;
            transform: translateY(-100%);
            opacity: 0;
        }

        .welcome-bar.show {
            transform: translateY(0);
            opacity: 1;
        }
    </style>


    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Check localStorage for closed bars
            document.querySelectorAll('.welcome-bar').forEach(function (bar) {
                const barId = bar.getAttribute('id');
                const closedTimestamp = localStorage.getItem(barId);
                if (closedTimestamp) {
                    const now = new Date().getTime();
                    const elapsed = now - parseInt(closedTimestamp, 10);
                    const oneDay = 24 * 60 * 60 * 1000; // 24 hours in milliseconds
                    if (elapsed < oneDay) {
                        bar.remove();
                    } else {
                        localStorage.removeItem(barId); // Clear expired timestamp
                        bar.classList.add('show'); // Show the bar with animation
                    }
                } else {
                    bar.classList.add('show'); // Show the bar with animation
                }
            });

            // Simple close-button logic
            document.querySelectorAll('.welcome-bar-close').forEach(function (btn) {
                btn.addEventListener('click', function (e) {
                    const bar = e.target.closest('.welcome-bar');
                    if (bar) {
                        const barId = bar.getAttribute('id');
                        const autoHide = bar.getAttribute('data-auto-hide') === 'true';
                        if (!autoHide) {
                            const now = new Date().getTime();
                            localStorage.setItem(barId, now.toString());
                        }
                        bar.classList.remove('show'); // Hide the bar with animation
                        setTimeout(() => bar.remove(), 300); // Wait for animation to complete
                    }
                });
            });

            // Auto-hide logic
            document.querySelectorAll('.welcome-bar').forEach(function (bar) {
                const autoHide = bar.getAttribute('data-auto-hide') === 'true';
                const delay = parseInt(bar.getAttribute('data-auto-hide-delay'), 10);

                if (autoHide && !isNaN(delay)) {
                    setTimeout(function () {
                        bar.classList.remove('show'); // Hide the bar with animation
                        setTimeout(() => bar.remove(), 300); // Wait for animation to complete
                    }, delay);
                }
            });
        });
    </script>
@endif
