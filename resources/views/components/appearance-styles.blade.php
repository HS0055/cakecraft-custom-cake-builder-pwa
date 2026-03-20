@php
        try {
                $appearance = app(App\Settings\AppearanceSettings::class);
                $primary = $appearance->primary_color ?? '#E75A7D';
                $accent = $appearance->primary_color ?? '#29B6D6';
                $sidebar = $appearance->admin_sidebar_color ?? '#2D2D2D';
        } catch (\Throwable $e) {
                // Settings table may not exist during fresh install
                $primary = '#E75A7D';
                $accent = '#29B6D6';
                $sidebar = '#2D2D2D';
        }
@endphp

<style>
        :root {
                /* Override design-system semantic tokens with appearance settings */
                --color-primary:
                        {{ $primary }}
                ;
                --color-primary-hover: color-mix(in srgb,
                                {{ $primary }}
                                , black 15%);
                --color-primary-foreground: #faf5f0;

                --color-accent:
                        {{ $accent }}
                ;
                --color-accent-hover: color-mix(in srgb,
                                {{ $accent }}
                                , black 12%);
                --color-accent-foreground: color-mix(in srgb,
                                {{ $accent }}
                                , black 60%);

                --color-ring:
                        {{ $accent }}
                ;
        }

        /* Admin sidebar override */
        :root {
                --color-sidebar:
                        {{ $sidebar }}
                ;
                --color-sidebar-hover: color-mix(in srgb,
                                {{ $sidebar }}
                                , white 8%);
                --color-sidebar-active: color-mix(in srgb,
                                {{ $sidebar }}
                                , var(--color-accent) 30%);
                --color-sidebar-text: color-mix(in srgb,
                                {{ $sidebar }}
                                , white 75%);
                --color-sidebar-text-muted: color-mix(in srgb,
                                {{ $sidebar }}
                                , white 45%);
                --color-sidebar-border: color-mix(in srgb,
                                {{ $sidebar }}
                                , white 12%);
        }

        /* Storefront color overrides — derive from primary_color */
        :root {
                --color-pink:
                        {{ $primary }}
                ;
                --color-pink-dark: color-mix(in srgb,
                                {{ $primary }}
                                , black 20%);
                --color-blush: color-mix(in srgb,
                                {{ $primary }}
                                , white 60%);
                --color-frosting: color-mix(in srgb,
                                {{ $primary }}
                                , white 80%);
                --color-primary-warm: color-mix(in srgb,
                                {{ $primary }}
                                , white 40%);
                --shadow-glow-pink: 0 0 30px color-mix(in srgb,
                                {{ $primary }}
                                , transparent 80%);
        }
</style>