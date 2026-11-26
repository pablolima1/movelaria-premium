import flatpickr from 'flatpickr';
import { Portuguese } from 'flatpickr/dist/esm/l10n/pt.js';

/**
 * x-datepicker: calendário (flatpickr) em português sobre um input com x-model.
 * O valor enviado continua "Y-m-d"; o usuário vê "d/m/Y".
 * Opcional: x-datepicker="{ minDate: form.data_inicio }" (reativo).
 */
export function registerDatepicker(Alpine) {
    Alpine.directive('datepicker', (el, { expression }, { evaluateLater, effect, cleanup }) => {
        const getOptions = expression ? evaluateLater(expression) : null;
        const required = el.required;

        const syncModel = () => el.dispatchEvent(new Event('input', { bubbles: true }));

        const fp = flatpickr(el, {
            locale: Portuguese,
            dateFormat: 'Y-m-d',
            altInput: true,
            altFormat: 'd/m/Y',
            disableMobile: true, // mesmo calendário no celular e no computador
            monthSelectorType: 'static',
            onChange: syncModel,
            onReady: (_dates, _str, instance) => {
                const footer = document.createElement('div');
                footer.className = 'mt-4 flex items-center justify-between gap-2 border-t border-gray-100 pt-3 dark:border-gray-800';

                const button = (label, onClick, extra = '') => {
                    const b = document.createElement('button');
                    b.type = 'button';
                    b.textContent = label;
                    b.className = `rounded-lg px-3 py-2 text-sm font-medium ${extra}`;
                    b.addEventListener('click', onClick);
                    return b;
                };

                footer.appendChild(button('Hoje', () => {
                    instance.setDate(new Date(), true);
                    instance.close();
                }, 'text-brand-500 hover:bg-brand-50 dark:hover:bg-brand-500/10'));

                if (!required) {
                    footer.appendChild(button('Limpar', () => {
                        instance.clear();
                        instance.close();
                    }, 'text-gray-500 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-white/5'));
                }

                instance.calendarContainer.appendChild(footer);
            },
        });

        // Modelo -> calendário (ex.: modal de edição preenchendo o form)
        effect(() => {
            const value = el._x_model ? el._x_model.get() : el.value;
            const current = fp.selectedDates[0] ? fp.formatDate(fp.selectedDates[0], 'Y-m-d') : '';
            if ((value || '') !== current) {
                fp.setDate(value || null, false);
            }
        });

        if (getOptions) {
            effect(() => getOptions((options) => {
                if (options && 'minDate' in options) {
                    fp.set('minDate', options.minDate || null);
                }
            }));
        }

        cleanup(() => fp.destroy());
    });
}

/**
 * Formata dígitos como moeda pt-BR preenchendo da direita (centavos primeiro):
 * "2" -> "0,02", "2000" -> "20,00", "200000" -> "2.000,00".
 */
export function formatMoney(value) {
    const digits = String(value ?? '').replace(/\D/g, '').replace(/^0+/, '');
    if (digits === '') {
        return '';
    }

    const padded = digits.padStart(3, '0');
    const inteiro = padded.slice(0, -2).replace(/\B(?=(\d{3})+(?!\d))/g, '.');

    return `${inteiro},${padded.slice(-2)}`;
}

/**
 * x-money: máscara de valor em R$ (estilo caixa registradora). Cada dígito
 * digitado entra pela direita: 20,00 -> 200,00 -> 2.000,00. Funciona junto
 * com x-model; valores vindos do modelo já formatados ("1.234,56") são mantidos.
 */
export function registerMoney(Alpine) {
    Alpine.directive('money', (el, _directive, { effect, cleanup }) => {
        const apply = () => {
            const formatted = formatMoney(el.value);
            if (formatted !== el.value) {
                el.value = formatted;
            }
            // Cursor sempre no fim: os dígitos entram pela direita.
            if (document.activeElement === el) {
                const end = el.value.length;
                el.setSelectionRange(end, end);
            }
        };

        // Captura: roda antes do listener do x-model, que então lê o valor formatado.
        el.addEventListener('input', apply, true);
        cleanup(() => el.removeEventListener('input', apply, true));

        // Modelo -> campo (ex.: modal de edição, botão "Saldo a receber").
        effect(() => {
            const value = el._x_model ? el._x_model.get() : el.value;
            const formatted = formatMoney(value);
            if ((value ?? '') !== formatted && el._x_model) {
                el._x_model.set(formatted);
            }
        });
    });
}
