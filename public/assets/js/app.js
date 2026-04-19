document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-password-toggle]').forEach((button) => {
        if (button.hasAttribute('data-password-press-hold')) {
            return;
        }

        button.addEventListener('click', () => {
            const target = document.getElementById(button.dataset.target);

            if (!target) {
                return;
            }

            const isPassword = target.getAttribute('type') === 'password';
            target.setAttribute('type', isPassword ? 'text' : 'password');
            const showLabel = button.dataset.labelShow || 'Show';
            const hideLabel = button.dataset.labelHide || 'Hide';
            const currentLabel = isPassword ? hideLabel : showLabel;
            const icon = button.querySelector('.password-toggle-icon');

            button.setAttribute('aria-label', currentLabel);
            button.setAttribute('aria-pressed', isPassword ? 'true' : 'false');

            if (icon && button.hasAttribute('data-password-icon-toggle')) {
                icon.classList.toggle('bi-eye', !isPassword);
                icon.classList.toggle('bi-eye-slash', isPassword);
                return;
            }

            button.textContent = currentLabel;
        });
    });

    document.querySelectorAll('form[data-confirm]').forEach((form) => {
        form.addEventListener('submit', (event) => {
            if (!window.confirm(form.dataset.confirm || 'Are you sure?')) {
                event.preventDefault();
            }
        });
    });

    document.querySelectorAll('[data-reset-filters]').forEach((button) => {
        button.addEventListener('click', () => {
            const form = document.querySelector(button.dataset.resetFilters);

            if (!form) {
                return;
            }

            form.reset();
            form.submit();
        });
    });

    document.querySelectorAll('[data-sidebar-toggle]').forEach((button) => {
        button.addEventListener('click', () => {
            document.getElementById('appShell')?.classList.toggle('sidebar-open');
        });
    });

    const transactionForm = document.querySelector('[data-transaction-form]');

    if (transactionForm) {
        const itemSelect = transactionForm.querySelector('[data-transaction-item]');
        const quantityIn = transactionForm.querySelector('[data-quantity-in]');
        const quantityOut = transactionForm.querySelector('[data-quantity-out]');
        const stockHint = document.getElementById('availableStockHint');

        const syncTransactionHelper = () => {
            const selectedOption = itemSelect?.selectedOptions?.[0];
            const currentStock = Number(selectedOption?.dataset.stock || 0);
            const unit = selectedOption?.dataset.unit || '';

            if (stockHint) {
                stockHint.textContent = itemSelect?.value
                    ? `Available stock: ${currentStock.toFixed(3)} ${unit.toUpperCase()}`
                    : 'Available stock will appear after you select an item.';
            }

            if (quantityIn && quantityOut) {
                quantityOut.max = currentStock > 0 ? String(currentStock) : '';

                if (Number(quantityIn.value) > 0) {
                    quantityOut.classList.add('bg-light');
                } else {
                    quantityOut.classList.remove('bg-light');
                }

                if (Number(quantityOut.value) > 0) {
                    quantityIn.classList.add('bg-light');
                } else {
                    quantityIn.classList.remove('bg-light');
                }
            }
        };

        itemSelect?.addEventListener('change', syncTransactionHelper);
        quantityIn?.addEventListener('input', syncTransactionHelper);
        quantityOut?.addEventListener('input', syncTransactionHelper);
        syncTransactionHelper();
    }

    const cottonEntryForm = document.querySelector('[data-cotton-entry-form]');

    if (cottonEntryForm) {
        const grossWeight = cottonEntryForm.querySelector('[data-gross-weight]');
        const tareWeight = cottonEntryForm.querySelector('[data-tare-weight]');
        const netWeight = cottonEntryForm.querySelector('[data-net-weight]');

        const syncNetWeight = () => {
            const grossValue = Number(grossWeight?.value || 0);
            const tareValue = Number(tareWeight?.value || 0);
            const calculated = Math.max(0, grossValue - tareValue);

            if (netWeight) {
                netWeight.value = calculated.toFixed(3);
            }

            if (grossWeight && tareWeight) {
                if (tareValue > grossValue && grossValue > 0) {
                    tareWeight.classList.add('is-invalid');
                } else {
                    tareWeight.classList.remove('is-invalid');
                }
            }
        };

        grossWeight?.addEventListener('input', syncNetWeight);
        tareWeight?.addEventListener('input', syncNetWeight);
        syncNetWeight();
    }

    const productionStageForm = document.querySelector('[data-production-stage-form]');

    if (productionStageForm) {
        const sourceSelect = productionStageForm.querySelector('[data-production-source]');
        const inputQuantity = productionStageForm.querySelector('[data-production-input]');
        const stockHint = document.getElementById('productionStockHint');
        const outputRows = productionStageForm.querySelector('[data-output-rows]');
        const outputRowTemplate = productionStageForm.querySelector('[data-output-row-template]');
        const addOutputButton = productionStageForm.querySelector('[data-add-output-row]');
        const totalOutputElement = document.getElementById('productionOutputTotal');

        const syncSourceStockHint = () => {
            const selectedOption = sourceSelect?.selectedOptions?.[0];
            const currentStock = Number(selectedOption?.dataset.stock || 0);
            const unit = selectedOption?.dataset.unit || '';

            if (stockHint) {
                stockHint.textContent = sourceSelect?.value
                    ? `Available stock: ${currentStock.toFixed(3)} ${unit.toUpperCase()}`
                    : 'Available stock will appear after you select a source material.';
            }

            if (inputQuantity) {
                inputQuantity.max = currentStock > 0 ? String(currentStock) : '';
            }
        };

        const syncOutputRowUnit = (row) => {
            const itemSelect = row.querySelector('[data-output-item]');
            const unitField = row.querySelector('[data-output-unit]');
            const selectedOption = itemSelect?.selectedOptions?.[0];

            if (unitField) {
                unitField.value = selectedOption?.dataset.unit || '';
            }
        };

        const syncOutputTotal = () => {
            const total = Array.from(productionStageForm.querySelectorAll('[data-output-quantity]'))
                .reduce((sum, field) => sum + Number(field.value || 0), 0);

            if (totalOutputElement) {
                totalOutputElement.textContent = total.toFixed(3);
            }
        };

        const reindexOutputRows = () => {
            Array.from(outputRows?.querySelectorAll('[data-output-row]') || []).forEach((row, index) => {
                row.querySelectorAll('input, select').forEach((field) => {
                    const name = field.getAttribute('name');

                    if (!name) {
                        return;
                    }

                    field.setAttribute('name', name.replace(/outputs\[\d+\]/, `outputs[${index}]`));
                });
            });
        };

        const bindOutputRow = (row) => {
            row.querySelector('[data-output-item]')?.addEventListener('change', () => {
                syncOutputRowUnit(row);
            });

            row.querySelector('[data-output-quantity]')?.addEventListener('input', syncOutputTotal);

            row.querySelector('[data-remove-output-row]')?.addEventListener('click', () => {
                const rowCount = outputRows?.querySelectorAll('[data-output-row]').length || 0;

                if (rowCount <= 1) {
                    return;
                }

                row.remove();
                reindexOutputRows();
                syncOutputTotal();
            });

            syncOutputRowUnit(row);
        };

        addOutputButton?.addEventListener('click', () => {
            if (!outputRows || !outputRowTemplate) {
                return;
            }

            const index = outputRows.querySelectorAll('[data-output-row]').length;
            const html = outputRowTemplate.innerHTML
                .replace('__NAME__', `outputs[${index}][inventory_item_id]`)
                .replace('__TYPE_NAME__', `outputs[${index}][output_type]`)
                .replace('__QUANTITY_NAME__', `outputs[${index}][quantity]`)
                .replace('__UNIT_NAME__', `outputs[${index}][unit]`);

            outputRows.insertAdjacentHTML('beforeend', html);
            const newRow = outputRows.querySelectorAll('[data-output-row]')[index];

            if (newRow) {
                bindOutputRow(newRow);
            }

            syncOutputTotal();
        });

        outputRows?.querySelectorAll('[data-output-row]').forEach((row) => {
            bindOutputRow(row);
        });

        sourceSelect?.addEventListener('change', syncSourceStockHint);
        inputQuantity?.addEventListener('input', syncSourceStockHint);

        syncSourceStockHint();
        syncOutputTotal();
    }

    const saleForm = document.querySelector('[data-sale-form]');

    if (saleForm) {
        const rowsContainer = saleForm.querySelector('[data-sale-item-rows]');
        const template = saleForm.querySelector('[data-sale-item-template]');
        const addButton = saleForm.querySelector('[data-add-sale-item]');
        const paidInput = saleForm.querySelector('[data-sale-paid]');
        const grandTotalElement = saleForm.querySelector('[data-sale-grand-total]');
        const paidPreviewElement = saleForm.querySelector('[data-sale-paid-preview]');
        const remainingElement = saleForm.querySelector('[data-sale-remaining]');

        const syncRow = (row) => {
            const select = row.querySelector('[data-sale-item-select]');
            const quantity = row.querySelector('[data-sale-quantity]');
            const unitPrice = row.querySelector('[data-sale-unit-price]');
            const stockDisplay = row.querySelector('[data-sale-stock-display]');
            const unitDisplay = row.querySelector('[data-sale-unit-display]');
            const lineTotal = row.querySelector('[data-sale-line-total]');
            const option = select?.selectedOptions?.[0];
            const stock = Number(option?.dataset.stock || 0);
            const unit = option?.dataset.unit || '--';
            const total = Number(quantity?.value || 0) * Number(unitPrice?.value || 0);

            if (stockDisplay) {
                stockDisplay.textContent = stock.toFixed(3);
            }

            if (unitDisplay) {
                unitDisplay.textContent = unit.toUpperCase();
            }

            if (quantity) {
                quantity.max = stock > 0 ? String(stock) : '';
            }

            if (lineTotal) {
                lineTotal.textContent = total.toFixed(2);
            }
        };

        const syncTotals = () => {
            const grandTotal = Array.from(saleForm.querySelectorAll('[data-sale-item-row]')).reduce((sum, row) => {
                const quantity = Number(row.querySelector('[data-sale-quantity]')?.value || 0);
                const unitPrice = Number(row.querySelector('[data-sale-unit-price]')?.value || 0);

                return sum + (quantity * unitPrice);
            }, 0);

            const paidAmount = Number(paidInput?.value || 0);
            const remaining = Math.max(0, grandTotal - paidAmount);

            grandTotalElement.textContent = grandTotal.toFixed(2);
            paidPreviewElement.textContent = paidAmount.toFixed(2);
            remainingElement.textContent = remaining.toFixed(2);
        };

        const reindexRows = () => {
            Array.from(rowsContainer?.querySelectorAll('[data-sale-item-row]') || []).forEach((row, index) => {
                row.querySelectorAll('input, select').forEach((field) => {
                    const name = field.getAttribute('name');

                    if (name) {
                        field.setAttribute('name', name.replace(/items\[\d+\]/, `items[${index}]`));
                    }
                });
            });
        };

        const bindRow = (row) => {
            row.querySelector('[data-sale-item-select]')?.addEventListener('change', () => {
                syncRow(row);
                syncTotals();
            });

            row.querySelector('[data-sale-quantity]')?.addEventListener('input', () => {
                syncRow(row);
                syncTotals();
            });

            row.querySelector('[data-sale-unit-price]')?.addEventListener('input', () => {
                syncRow(row);
                syncTotals();
            });

            row.querySelector('[data-remove-sale-item]')?.addEventListener('click', () => {
                const rowCount = rowsContainer?.querySelectorAll('[data-sale-item-row]').length || 0;

                if (rowCount <= 1) {
                    return;
                }

                row.remove();
                reindexRows();
                syncTotals();
            });

            syncRow(row);
        };

        addButton?.addEventListener('click', () => {
            if (!rowsContainer || !template) {
                return;
            }

            const index = rowsContainer.querySelectorAll('[data-sale-item-row]').length;
            rowsContainer.insertAdjacentHTML('beforeend', template.innerHTML.replaceAll('__INDEX__', String(index)));
            const row = rowsContainer.querySelectorAll('[data-sale-item-row]')[index];

            if (row) {
                bindRow(row);
            }

            syncTotals();
        });

        rowsContainer?.querySelectorAll('[data-sale-item-row]').forEach((row) => bindRow(row));
        paidInput?.addEventListener('input', syncTotals);
        syncTotals();
    }

    const salePaymentForm = document.querySelector('[data-sale-payment-form]');

    if (salePaymentForm) {
        const amountInput = salePaymentForm.querySelector('[data-payment-amount]');
        const remainingInput = salePaymentForm.querySelector('[data-payment-current-remaining]');
        const afterElement = salePaymentForm.querySelector('[data-payment-after]');

        const syncPaymentBalance = () => {
            const currentRemaining = Number(remainingInput?.value || 0);
            const paymentAmount = Number(amountInput?.value || 0);
            const remainingAfter = Math.max(0, currentRemaining - paymentAmount);

            if (afterElement) {
                afterElement.textContent = remainingAfter.toFixed(2);
            }
        };

        amountInput?.addEventListener('input', syncPaymentBalance);
        syncPaymentBalance();
    }

    document.querySelectorAll('[data-salary-input]').forEach((input) => {
        input.addEventListener('blur', () => {
            const value = Number(input.value || 0);

            if (!Number.isNaN(value)) {
                input.value = value.toFixed(2);
            }
        });
    });

    const payrollForm = document.querySelector('[data-payroll-form]');

    if (payrollForm) {
        const employeeSelect = payrollForm.querySelector('[data-payroll-employee]');
        const basicSalaryInput = payrollForm.querySelector('[data-payroll-basic]');
        const bonusInput = payrollForm.querySelector('[data-payroll-bonus]');
        const deductionInput = payrollForm.querySelector('[data-payroll-deduction]');
        const basicPreview = payrollForm.querySelector('[data-payroll-basic-preview]');
        const bonusPreview = payrollForm.querySelector('[data-payroll-bonus-preview]');
        const deductionPreview = payrollForm.querySelector('[data-payroll-deduction-preview]');
        const advancePreview = payrollForm.querySelector('[data-payroll-advance-preview]');
        const grossPreview = payrollForm.querySelector('[data-payroll-gross-preview]');
        const totalPreview = payrollForm.querySelector('[data-payroll-total]');

        const syncPayrollTotals = () => {
            const selectedOption = employeeSelect?.selectedOptions?.[0];

            if (basicSalaryInput && selectedOption?.dataset.salary && !basicSalaryInput.value) {
                basicSalaryInput.value = selectedOption.dataset.salary;
            }

            const basic = Number(basicSalaryInput?.value || 0);
            const bonus = Number(bonusInput?.value || 0);
            const deduction = Number(deductionInput?.value || 0);
            const advance = Number(selectedOption?.dataset.pendingAdvance || 0);
            const gross = Math.max(0, basic + bonus - deduction);
            const total = Math.max(0, gross - advance);

            if (basicPreview) basicPreview.textContent = basic.toFixed(2);
            if (bonusPreview) bonusPreview.textContent = bonus.toFixed(2);
            if (deductionPreview) deductionPreview.textContent = deduction.toFixed(2);
            if (advancePreview) advancePreview.textContent = advance.toFixed(2);
            if (grossPreview) grossPreview.textContent = gross.toFixed(2);
            if (totalPreview) totalPreview.textContent = total.toFixed(2);
        };

        employeeSelect?.addEventListener('change', () => {
            if (basicSalaryInput) {
                basicSalaryInput.value = employeeSelect.selectedOptions?.[0]?.dataset.salary || '0.00';
            }

            syncPayrollTotals();
        });

        [basicSalaryInput, bonusInput, deductionInput].forEach((input) => {
            input?.addEventListener('input', syncPayrollTotals);
        });

        syncPayrollTotals();
    }

    const salaryPaymentForm = document.querySelector('[data-salary-payment-form]');

    if (salaryPaymentForm) {
        const amountInput = salaryPaymentForm.querySelector('[data-salary-payment-amount]');
        const remainingInput = salaryPaymentForm.querySelector('[data-salary-payment-remaining]');
        const remainingAfter = salaryPaymentForm.querySelector('[data-salary-payment-after]');

        const syncRemainingSalary = () => {
            const currentRemaining = Number(remainingInput?.value || 0);
            const paymentAmount = Number(amountInput?.value || 0);
            const nextRemaining = Math.max(0, currentRemaining - paymentAmount);

            if (remainingAfter) {
                remainingAfter.textContent = nextRemaining.toFixed(2);
            }
        };

        amountInput?.addEventListener('input', syncRemainingSalary);
        syncRemainingSalary();
    }

    const advanceForm = document.querySelector('[data-advance-form]');

    if (advanceForm) {
        const employeeSelect = advanceForm.querySelector('[data-advance-employee]');
        const amountInput = advanceForm.querySelector('[data-advance-amount]');
        const preview = advanceForm.querySelector('[data-advance-preview]');
        const pendingPreview = advanceForm.querySelector('[data-advance-pending-preview]');
        const totalPreview = advanceForm.querySelector('[data-advance-total-preview]');

        const syncAdvancePreview = () => {
            const selectedOption = employeeSelect?.selectedOptions?.[0];
            const pending = Number(selectedOption?.dataset.pendingAdvance || 0);
            const amount = Number(amountInput?.value || 0);

            if (preview) preview.textContent = amount.toFixed(2);
            if (pendingPreview) pendingPreview.textContent = pending.toFixed(2);
            if (totalPreview) totalPreview.textContent = (pending + amount).toFixed(2);
        };

        employeeSelect?.addEventListener('change', syncAdvancePreview);
        amountInput?.addEventListener('input', syncAdvancePreview);
        syncAdvancePreview();
    }
});
