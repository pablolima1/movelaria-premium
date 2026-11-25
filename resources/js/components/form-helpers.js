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
 * Campos com máscara $money: ao sair do campo completa os centavos
 * ("20.000" -> "20.000,00"), deixando claro o valor que será salvo.
 */
export function registerMoneyBlur() {
    document.addEventListener('blur', (event) => {
        const el = event.target;
        if (!(el instanceof HTMLInputElement) || !(el.getAttribute('x-mask:dynamic') || '').includes('$money')) {
            return;
        }

        const value = el.value.trim();
        if (value === '') {
            return;
        }

        const [inteiro, decimal = ''] = value.split(',');
        const padded = `${inteiro || '0'},${(decimal + '00').slice(0, 2)}`;

        if (padded !== value) {
            el.value = padded;
            el.dispatchEvent(new Event('input', { bubbles: true }));
        }
    }, true);
}
