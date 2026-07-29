document.addEventListener('DOMContentLoaded', function () {
    const CART_URL = 'cart-action.php';

    // Robust JSON parser to strip any PHP startup warnings/notices
    function parseJSONClean(response) {
        return response.text().then(function(text) {
            const start = text.indexOf('{');
            if (start === -1) throw new Error('No JSON object found: ' + text);
            return JSON.parse(text.substring(start));
        });
    }

    document.querySelectorAll('.add-to-cart').forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            const productId = btn.dataset.id;
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Adding...';
            fetch(CART_URL, {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'action=add&product_id=' + productId
            })
            .then(parseJSONClean)
            .then(function(data) {
                if (data.success) {
                    btn.innerHTML = '<i class="bi bi-check2 me-1"></i>Added!';
                    btn.classList.remove('btn-primary'); btn.classList.add('btn-success');
                    updateCartBadge(data.cart_count);
                    setTimeout(function() {
                        btn.innerHTML = '<i class="bi bi-cart-plus me-1"></i>Add to Cart';
                        btn.classList.remove('btn-success'); btn.classList.add('btn-primary');
                        btn.disabled = false;
                    }, 2000);
                } else if (data.redirect) {
                    window.location.href = data.redirect;
                } else {
                    alert(data.message || 'Failed to add to cart.');
                    btn.innerHTML = '<i class="bi bi-cart-plus me-1"></i>Add to Cart';
                    btn.disabled = false;
                }
            })
            .catch(function() {
                btn.innerHTML = '<i class="bi bi-cart-plus me-1"></i>Add to Cart';
                btn.disabled = false;
            });
        });
    });

    function updateCartBadge(count) {
        let badge = document.querySelector('.cart-badge');
        const icon = document.querySelector('.cart-icon');
        if (count > 0) {
            if (!badge && icon) { 
                badge = document.createElement('span'); 
                badge.className = 'cart-badge bg-primary text-white text-[9px] font-bold rounded-full w-5 h-5 flex items-center justify-center border-2 border-white -mt-2 -ml-1'; 
                icon.appendChild(badge); 
            }
            if (badge) badge.textContent = count;
        } else if (badge) { badge.remove(); }
    }

    document.querySelectorAll('.qty-btn').forEach(function(btn) {
        btn.addEventListener('click', function () {
            const row = btn.closest('tr');
            const productId = btn.dataset.id;
            const action = btn.dataset.action;
            const qtyEl = row.querySelector('.qty-display');
            let qty = parseInt(qtyEl.textContent);
            if (action === 'increase') qty++;
            else if (action === 'decrease' && qty > 1) qty--;
            else if (action === 'decrease' && qty === 1) { removeItem(productId, row); return; }
            fetch(CART_URL, {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'action=update&product_id=' + productId + '&quantity=' + qty
            })
            .then(parseJSONClean)
            .then(function(data) {
                if (data.success) {
                    qtyEl.textContent = qty;
                    var it = row.querySelector('.item-total');
                    if (it) it.textContent = 'NPR ' + parseFloat(data.item_total).toFixed(2);
                    
                    // Update checkbox data attribute
                    const checkbox = row.querySelector('.item-checkbox');
                    if (checkbox) {
                        checkbox.dataset.qty = qty;
                    }
                    
                    updateTotals();
                    updateCartBadge(data.cart_count);
                } else {
                    alert(data.message || 'Failed to update quantity.');
                    location.reload();
                }
            })
            .catch(function(err) {
                console.error('Error updating quantity:', err);
                alert('An error occurred while updating quantity: ' + err.message);
            });
        });
    });

    document.querySelectorAll('.remove-item').forEach(function(btn) {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            removeItem(btn.dataset.id, btn.closest('tr'));
        });
    });

    function removeItem(productId, row) {
        if (!confirm('Are you sure you want to remove this product from the cart?')) return;
        fetch(CART_URL, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'action=remove&product_id=' + productId
        })
        .then(parseJSONClean)
        .then(function(data) {
            if (data.success) {
                if (row) row.remove();
                updateCartBadge(data.cart_count);
                if (data.cart_count === 0) {
                    location.reload();
                } else {
                    updateTotals();
                }
            } else {
                alert(data.message || 'Failed to remove item from cart.');
            }
        })
        .catch(function(err) {
            console.error('Error removing item:', err);
            alert('An error occurred while removing the item: ' + err.message);
        });
    }

    // Checkbox selection logic
    const selectAllCheckbox = document.getElementById('selectAll');
    const checkoutBtn = document.getElementById('checkoutBtn');

    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function () {
            document.querySelectorAll('.item-checkbox').forEach(cb => cb.checked = selectAllCheckbox.checked);
            updateTotals();
        });
    }

    document.querySelectorAll('.item-checkbox').forEach(function (cb) {
        cb.addEventListener('change', function () {
            if (selectAllCheckbox) {
                const activeCbs = document.querySelectorAll('.item-checkbox');
                const allChecked = Array.from(activeCbs).every(c => c.checked);
                selectAllCheckbox.checked = allChecked;
            }
            updateTotals();
        });
    });

    function updateTotals() {
        let subtotal = 0;
        let checkedCount = 0;
        let totalItemsCount = 0;
        const activeCheckboxes = document.querySelectorAll('.item-checkbox');

        activeCheckboxes.forEach(function (cb) {
            if (cb.checked) {
                const price = parseFloat(cb.dataset.price);
                const qty = parseInt(cb.dataset.qty);
                subtotal += price * qty;
                totalItemsCount += qty;
                checkedCount++;
            }
        });

        // Update main header badge next to title if it exists
        const headerBadge = document.getElementById('cartHeaderBadge');
        if (headerBadge) {
            headerBadge.textContent = `${totalItemsCount} item${totalItemsCount !== 1 ? 's' : ''}`;
            if (totalItemsCount === 0) {
                headerBadge.classList.add('hidden');
            } else {
                headerBadge.classList.remove('hidden');
            }
        }

        // Update Subtotal UI
        const subtotalLabel = document.getElementById('summarySubtotalLabel');
        if (subtotalLabel) {
            subtotalLabel.textContent = `Subtotal (${totalItemsCount} item${totalItemsCount !== 1 ? 's' : ''})`;
        }
        const subtotalValue = document.getElementById('summarySubtotal');
        if (subtotalValue) {
            subtotalValue.textContent = 'NPR ' + subtotal.toFixed(2);
        }

        // Calculate Shipping
        let shipping = 0;
        if (checkedCount > 0) {
            shipping = subtotal >= 2000 ? 0 : 150;
        }

        // Update Shipping UI
        const shippingValue = document.getElementById('summaryShipping');
        if (shippingValue) {
            shippingValue.innerHTML = shipping === 0 && checkedCount > 0
                ? '<span class="text-green-600 font-bold">FREE</span>'
                : 'NPR ' + shipping.toFixed(2);
        }

        // Update shipping threshold alert
        const alertContainer = document.getElementById('shippingAlertContainer');
        if (alertContainer) {
            if (checkedCount > 0 && shipping > 0) {
                const difference = 2000 - subtotal;
                alertContainer.innerHTML = `<div class="bg-blue-50 border border-blue-200 text-blue-800 rounded-lg p-3 text-xs font-semibold flex items-center gap-1.5 shipping-info-alert"><i class="bi bi-truck"></i>Add NPR <span id="shippingThresholdValue">${difference.toFixed(2)}</span> more for free shipping!</div>`;
            } else {
                alertContainer.innerHTML = '';
            }
        }

        // Calculate Grand Total
        const grandTotal = subtotal + shipping;
        const cartTotal = document.getElementById('cartTotal');
        if (cartTotal) {
            cartTotal.textContent = 'NPR ' + grandTotal.toFixed(2);
        }

        // Enable/Disable checkout button
        if (checkoutBtn) {
            if (checkedCount === 0) {
                checkoutBtn.disabled = true;
                checkoutBtn.classList.add('disabled');
            } else {
                checkoutBtn.disabled = false;
                checkoutBtn.classList.remove('disabled');
            }
        }
    }
});
