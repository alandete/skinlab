<section class="ui-kit">

    <!-- ══════════ TIPOGRAFÍA ══════════ -->
    <div class="ui-section">
        <h2>Tipografía</h2>
        <div class="ui-samples">
            <p style="font-size:var(--sl-text-2xl);font-weight:var(--sl-weight-bold);">Heading 2xl — 2rem (32px)</p>
            <p style="font-size:var(--sl-text-xl);font-weight:var(--sl-weight-bold);">Heading xl — 1.5rem (24px)</p>
            <p style="font-size:var(--sl-text-lg);font-weight:var(--sl-weight-bold);">Heading lg — 1.25rem (20px)</p>
            <p style="font-size:var(--sl-text-md);font-weight:var(--sl-weight-semibold);">Body md — 1.0625rem (17px)</p>
            <p style="font-size:var(--sl-text-base);">Body base — 1rem (16px) — texto por defecto</p>
            <p style="font-size:var(--sl-text-sm);color:var(--sl-text-light);">Small sm — 0.875rem (14px) — hints, metadata</p>
            <p style="font-size:var(--sl-text-xs);color:var(--sl-text-muted);text-transform:uppercase;letter-spacing:0.04em;">Extra small xs — 0.8125rem (13px) — badges, tags</p>
        </div>
        <div class="ui-tokens">
            <code>--sl-text-xs</code> <code>--sl-text-sm</code> <code>--sl-text-base</code> <code>--sl-text-md</code> <code>--sl-text-lg</code> <code>--sl-text-xl</code> <code>--sl-text-2xl</code>
        </div>
    </div>

    <!-- ══════════ COLORES ══════════ -->
    <div class="ui-section">
        <h2>Colores</h2>
        <div class="ui-color-grid">
            <div class="ui-color-swatch" style="background:var(--sl-primary);color:#fff;">--sl-primary</div>
            <div class="ui-color-swatch" style="background:var(--sl-primary-hover);color:#fff;">--sl-primary-hover</div>
            <div class="ui-color-swatch" style="background:var(--sl-primary-dark);color:#fff;">--sl-primary-dark</div>
            <div class="ui-color-swatch" style="background:var(--sl-secondary);color:#fff;">--sl-secondary</div>
            <div class="ui-color-swatch" style="background:var(--sl-secondary-light);color:#fff;">--sl-secondary-light</div>
            <div class="ui-color-swatch" style="background:var(--sl-success);color:#fff;">--sl-success</div>
            <div class="ui-color-swatch" style="background:var(--sl-danger);color:#fff;">--sl-danger</div>
            <div class="ui-color-swatch" style="background:var(--sl-warning);color:#fff;">--sl-warning</div>
            <div class="ui-color-swatch" style="background:var(--sl-text);">--sl-text</div>
            <div class="ui-color-swatch" style="background:var(--sl-text-light);color:#fff;">--sl-text-light</div>
            <div class="ui-color-swatch" style="background:var(--sl-text-muted);color:#fff;">--sl-text-muted</div>
            <div class="ui-color-swatch" style="background:var(--sl-bg);border:1px solid var(--sl-border-light);">--sl-bg</div>
            <div class="ui-color-swatch" style="background:var(--sl-bg-white);border:1px solid var(--sl-border-light);">--sl-bg-white</div>
            <div class="ui-color-swatch" style="background:var(--sl-bg-dark);color:#fff;">--sl-bg-dark</div>
            <div class="ui-color-swatch" style="background:var(--sl-border-light);border:1px solid var(--sl-border);">--sl-border-light</div>
            <div class="ui-color-swatch" style="background:var(--sl-border);">--sl-border</div>
        </div>
    </div>

    <!-- ══════════ ESPACIADO ══════════ -->
    <div class="ui-section">
        <h2>Espaciado</h2>
        <div class="ui-spacing-grid">
            <div class="ui-spacing-item"><span class="ui-spacing-bar" style="width:var(--sl-space-1);"></span> <code>--sl-space-1</code> 4px</div>
            <div class="ui-spacing-item"><span class="ui-spacing-bar" style="width:var(--sl-space-2);"></span> <code>--sl-space-2</code> 8px</div>
            <div class="ui-spacing-item"><span class="ui-spacing-bar" style="width:var(--sl-space-3);"></span> <code>--sl-space-3</code> 12px</div>
            <div class="ui-spacing-item"><span class="ui-spacing-bar" style="width:var(--sl-space-4);"></span> <code>--sl-space-4</code> 16px</div>
            <div class="ui-spacing-item"><span class="ui-spacing-bar" style="width:var(--sl-space-5);"></span> <code>--sl-space-5</code> 20px</div>
            <div class="ui-spacing-item"><span class="ui-spacing-bar" style="width:var(--sl-space-6);"></span> <code>--sl-space-6</code> 24px</div>
            <div class="ui-spacing-item"><span class="ui-spacing-bar" style="width:var(--sl-space-8);"></span> <code>--sl-space-8</code> 32px</div>
            <div class="ui-spacing-item"><span class="ui-spacing-bar" style="width:var(--sl-space-10);"></span> <code>--sl-space-10</code> 40px</div>
            <div class="ui-spacing-item"><span class="ui-spacing-bar" style="width:var(--sl-space-12);"></span> <code>--sl-space-12</code> 48px</div>
        </div>
    </div>

    <!-- ══════════ BOTONES ══════════ -->
    <div class="ui-section">
        <h2>Botones</h2>
        <div class="ui-row">
            <button class="btn btn-primary"><i class="bi bi-check-lg" aria-hidden="true"></i> Primary</button>
            <button class="btn btn-secondary">Secondary</button>
            <button class="btn btn-danger"><i class="bi bi-trash" aria-hidden="true"></i> Danger</button>
            <button class="btn btn-primary" disabled>Disabled</button>
        </div>
        <div class="ui-row">
            <button class="btn btn-primary btn-sm">Small Primary</button>
            <button class="btn btn-secondary btn-sm">Small Secondary</button>
            <button class="btn btn-danger btn-sm">Small Danger</button>
        </div>
        <div class="ui-tokens">
            <code>.btn</code> <code>.btn-primary</code> <code>.btn-secondary</code> <code>.btn-danger</code> <code>.btn-sm</code> <code>.btn-block</code>
        </div>
    </div>

    <!-- ══════════ FORMULARIOS ══════════ -->
    <div class="ui-section">
        <h2>Formularios</h2>
        <div class="ui-form-demo">
            <div class="form-group">
                <label class="form-label" for="demo-input">Label del campo</label>
                <input type="text" id="demo-input" class="form-input" placeholder="Placeholder del input">
                <p class="form-hint">Este es un texto de ayuda debajo del campo.</p>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="demo-select">Select</label>
                    <select id="demo-select" class="form-input">
                        <option>Opción 1</option>
                        <option>Opción 2</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label" for="demo-sm">Input small</label>
                    <input type="text" id="demo-sm" class="form-input form-input-sm" placeholder="Small">
                </div>
            </div>
        </div>
        <div class="ui-tokens">
            <code>.form-group</code> <code>.form-label</code> <code>.form-input</code> <code>.form-input-sm</code> <code>.form-hint</code> <code>.form-row</code>
        </div>
    </div>

    <!-- ══════════ ALERTAS ══════════ -->
    <div class="ui-section">
        <h2>Alertas</h2>
        <div class="alert alert-success"><i class="bi bi-check-circle" aria-hidden="true"></i> Operación exitosa.</div>
        <div class="alert alert-error"><i class="bi bi-exclamation-circle" aria-hidden="true"></i> Ha ocurrido un error.</div>
        <div class="alert alert-warning"><i class="bi bi-exclamation-triangle" aria-hidden="true"></i> Advertencia importante.</div>
        <div class="ui-tokens">
            <code>.alert</code> <code>.alert-success</code> <code>.alert-error</code> <code>.alert-warning</code>
        </div>
    </div>

    <!-- ══════════ BADGES ══════════ -->
    <div class="ui-section">
        <h2>Badges</h2>
        <div class="ui-row">
            <span class="badge badge-required">Obligatorio</span>
            <span class="badge badge-optional">Opcional</span>
            <span class="role-badge role-badge-admin">Admin</span>
            <span class="role-badge role-badge-editor">Editor</span>
            <span class="role-badge role-badge-guest">Invitado</span>
            <span class="status-badge status-inactive">Inactivo</span>
        </div>
        <div class="ui-tokens">
            <code>.badge</code> <code>.role-badge</code> <code>.status-badge</code>
        </div>
    </div>

    <!-- ══════════ CARDS ══════════ -->
    <div class="ui-section">
        <h2>Cards</h2>
        <div class="ui-row">
            <div class="card" style="max-width:20rem;">
                <h3 style="font-size:var(--sl-text-md);font-weight:var(--sl-weight-bold);margin-bottom:var(--sl-space-2);">Título de card</h3>
                <p style="color:var(--sl-text-light);font-size:var(--sl-text-sm);">Contenido de ejemplo dentro de una card estándar.</p>
            </div>
        </div>
        <div class="ui-tokens">
            <code>.card</code> — bg white, border light, radius-lg, padding space-5
        </div>
    </div>

    <!-- ══════════ TOASTS ══════════ -->
    <div class="ui-section">
        <h2>Toasts</h2>
        <div class="ui-row">
            <div class="sl-toast sl-toast-success" style="position:static;pointer-events:none;animation:none;">
                <i class="bi bi-check-circle" aria-hidden="true"></i> Operación exitosa
            </div>
            <div class="sl-toast sl-toast-error" style="position:static;pointer-events:none;animation:none;">
                <i class="bi bi-x-circle" aria-hidden="true"></i> Error al procesar
            </div>
        </div>
        <div class="ui-tokens">
            <code>.sl-toast</code> <code>.sl-toast-success</code> <code>.sl-toast-error</code> — centrado inferior, pill shape
        </div>
    </div>

    <!-- ══════════ BORDES Y RADIOS ══════════ -->
    <div class="ui-section">
        <h2>Bordes y Radios</h2>
        <div class="ui-row">
            <div class="ui-radius-demo" style="border-radius:var(--sl-radius);">radius<br><code>6px</code></div>
            <div class="ui-radius-demo" style="border-radius:var(--sl-radius-lg);">radius-lg<br><code>12px</code></div>
            <div class="ui-radius-demo" style="border-radius:var(--sl-radius-full);width:5rem;height:5rem;">radius-full</div>
        </div>
        <div class="ui-tokens">
            <code>--sl-radius</code> <code>--sl-radius-lg</code> <code>--sl-radius-full</code>
        </div>
    </div>

    <!-- ══════════ SOMBRAS ══════════ -->
    <div class="ui-section">
        <h2>Sombras</h2>
        <div class="ui-row">
            <div class="ui-shadow-demo" style="box-shadow:var(--sl-shadow-sm);">shadow-sm</div>
            <div class="ui-shadow-demo" style="box-shadow:var(--sl-shadow);">shadow</div>
            <div class="ui-shadow-demo" style="box-shadow:var(--sl-shadow-lg);">shadow-lg</div>
        </div>
    </div>

</section>

<style>
.ui-kit { max-width: 56rem; }

.ui-section {
    margin-bottom: var(--sl-space-8);
    padding-bottom: var(--sl-space-6);
    border-bottom: 1px solid var(--sl-border-light);
}

.ui-section:last-child { border-bottom: none; }

.ui-section h2 {
    font-size: var(--sl-text-xl);
    font-weight: var(--sl-weight-bold);
    color: var(--sl-text);
    margin-bottom: var(--sl-space-4);
}

.ui-samples p { margin-bottom: var(--sl-space-2); }

.ui-tokens {
    margin-top: var(--sl-space-4);
    display: flex;
    flex-wrap: wrap;
    gap: var(--sl-space-2);
}

.ui-tokens code {
    background: var(--sl-bg);
    padding: var(--sl-space-1) var(--sl-space-2);
    border-radius: var(--sl-radius);
    font-size: var(--sl-text-xs);
    color: var(--sl-primary);
}

.ui-row {
    display: flex;
    flex-wrap: wrap;
    gap: var(--sl-space-3);
    align-items: center;
    margin-bottom: var(--sl-space-3);
}

.ui-form-demo { max-width: 32rem; }

.ui-color-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(10rem, 1fr));
    gap: var(--sl-space-2);
}

.ui-color-swatch {
    padding: var(--sl-space-3);
    border-radius: var(--sl-radius);
    font-size: var(--sl-text-xs);
    font-family: var(--sl-font-mono);
}

.ui-spacing-grid {
    display: flex;
    flex-direction: column;
    gap: var(--sl-space-2);
}

.ui-spacing-item {
    display: flex;
    align-items: center;
    gap: var(--sl-space-3);
    font-size: var(--sl-text-sm);
    color: var(--sl-text-light);
}

.ui-spacing-bar {
    height: var(--sl-space-3);
    background: var(--sl-primary);
    border-radius: var(--sl-radius);
    display: inline-block;
}

.ui-radius-demo,
.ui-shadow-demo {
    width: 7rem;
    height: 5rem;
    border: 1px solid var(--sl-border-light);
    background: var(--sl-bg-white);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: var(--sl-text-sm);
    color: var(--sl-text-muted);
    text-align: center;
}
</style>
