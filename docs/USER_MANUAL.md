# CopyUS Shop — User Manual

## Table of Contents

1. [Introduction](#introduction)
2. [Getting an Account](#getting-an-account)
3. [Browsing the Shop](#browsing-the-shop)
4. [Shopping Cart & Checkout](#shopping-cart--checkout)
5. [Print-on-Demand](#print-on-demand)
6. [Orders & Invoices](#orders--invoices)
7. [Quotations](#quotations)
8. [B2B Company Accounts](#b2b-company-accounts)
9. [Wishlist](#wishlist)
10. [Support Tickets](#support-tickets)
11. [Your Profile](#your-profile)
12. [Language Selection](#language-selection)
13. [Admin Panel](#admin-panel)

---

## Introduction

CopyUS Shop is an online platform for ordering printed products and other customised goods. It is designed for both individual buyers and businesses (B2B). The platform is available in **Catalan**, **Spanish**, and **English**.

---

## Getting an Account

### Regular Account

1. Go to **Register** in the top navigation.
2. Fill in your name, email address, and a password.
3. Verify your email address using the link sent to your inbox.
4. You can now browse, add items to your cart, and place orders.

### B2B Account

Business accounts unlock exclusive pricing, quotation workflows, and company management tools.

1. Go to **Register** and choose the **B2B / Company** registration option.
2. Provide your company details including your fiscal identity (CIF/NIF/VAT).
3. Submit the form. Your account is placed in **pending** status.
4. An admin will review your application. You will receive an email when you are approved or rejected.
5. Once approved, you can access all B2B features.

---

## Browsing the Shop

### Homepage

The homepage showcases featured products and categories. Use the navigation bar to access the full product catalogue, print services, and company information pages.

### Product Catalogue

- Click **Products** in the navigation to browse the full catalogue.
- Use the **category sidebar** on the left to filter by product category.
- Use the **search bar** at the top to search by product name or keyword.

### Product Page

Each product page shows:
- Product images (click to zoom)
- Description and specifications
- Available variants (sizes, colours, etc.)
- Pricing (visible when `SHOW_PRICES` is enabled or for approved B2B users)
- Customer reviews and average rating
- Add to Cart / Add to Wishlist buttons

---

## Shopping Cart & Checkout

### Adding Items

From any product page:
1. Select the desired variant and quantity.
2. Click **Add to Cart**.
3. The cart icon in the header updates with the item count.

### Viewing the Cart

Click the cart icon or go to `/cart`. You can:
- Adjust quantities
- Remove items
- See the subtotal and any applicable discounts

### Applying a Promo Code

In the cart, enter a promotional code in the **Promo Code** field and click **Apply**. Valid codes reduce the total accordingly.

### Checkout

1. Click **Proceed to Checkout** from the cart.
2. Select or enter a shipping address. You can save addresses for future use.
3. Review the order summary.
4. Confirm the order.

You will receive an order confirmation email once the order is placed.

---

## Print-on-Demand

The print service allows you to customise and order printed products such as business cards, stickers, mugs, t-shirts, and more.

### Starting a Print Job

1. Go to **Print** in the navigation (or `/impressio`).
2. Browse available print templates.
3. Click on a template (e.g., **Business Cards**) to open the print builder.

### Configuring Your Print Job

In the print builder:
1. **Select options** — choose size, finish, paper type, colour mode, and other available options. Invalid combinations are automatically disabled.
2. **Enter quantity** — pricing updates dynamically based on volume tiers (larger quantities have lower unit prices).
3. **Upload artwork** — upload your design file(s) in the formats accepted by the template.
4. **Review pricing** — the total is calculated from your option selections and quantity.
5. **Add to Cart** — add the configured print job to your cart and proceed to checkout.

### Saved Print Configurations

If you regularly order the same print setup, save your configuration:
1. After configuring a print job, click **Save Configuration**.
2. Give it a name.
3. Access saved configurations from your dashboard under **Saved Print Configs** to reorder quickly.

### Tracking Print Jobs

Go to **My Orders** or **Print Jobs** from your dashboard to see the status of your print jobs:

| Status | Meaning |
|---|---|
| Pending | Order received, awaiting processing |
| In Production | Artwork confirmed, printing in progress |
| Shipped | Dispatched to delivery address |
| Completed | Delivered |
| Cancelled | Order cancelled |

---

## Orders & Invoices

### My Orders

Go to **Dashboard → Orders** to see all your orders. Click any order to view:
- Items ordered and quantities
- Order status
- Shipping address
- Total amount

### Invoices

Invoices are generated automatically for completed orders. Go to **Dashboard → Invoices** to view and download PDF invoices.

---

## Quotations

If you need pricing for a large or custom order that doesn't fit the standard catalogue, you can request a quotation.

### Requesting a Quote (Public Form)

1. Go to **Request a Quote** (`/demanar-pressupost`).
2. Fill in your contact details and describe what you need.
3. Submit the form. The team will contact you with a price.

### Quotations Dashboard (Authenticated)

Once the team prepares a quotation for you:
1. Go to **Dashboard → Quotations**.
2. View the itemised quotation with prices.
3. Accept the quotation to convert it into an order.

---

## B2B Company Accounts

Approved B2B users can manage a company account with multiple team members.

### Creating a Company

1. After your B2B account is approved, go to **Dashboard → My Company**.
2. Fill in your company name, address, and fiscal details.
3. Save the company profile.

### Inviting Team Members

1. From the company dashboard, go to **Members**.
2. Enter the email address of the person you want to invite.
3. Choose their role: **Manager** or **User**.
4. Send the invitation. They will receive an email with a link to join.

### Company Roles

| Role | Permissions |
|---|---|
| Owner | Full control — manage company, members, orders, and billing |
| Manager | Place orders, manage team members, view invoices |
| User | Place orders, view own orders |

---

## Wishlist

Save products for later without adding them to the cart.

1. On any product page, click the **heart icon** or **Add to Wishlist**.
2. View your wishlist at **Dashboard → Wishlist**.
3. Move items to cart directly from the wishlist.

---

## Support Tickets

For order issues, product questions, or general support:

1. Go to **Dashboard → Support** or **Tickets**.
2. Click **New Ticket**.
3. Select a subject category and describe your issue.
4. Submit the ticket.

You will receive email notifications when the support team replies. You can reply directly from the ticket thread.

---

## Your Profile

Go to **Dashboard → Profile** to:
- Update your name and email address
- Change your password
- Manage saved addresses (shipping and billing)
- Set your preferred language

### Saved Addresses

1. Go to **Profile → Addresses**.
2. Click **Add Address** to save a new address.
3. Set an address as **default** to pre-fill it at checkout.

---

## Language Selection

CopyUS Shop is available in three languages:

- **Català (ca)**
- **Español (es)**
- **English (en)**

Use the language switcher in the navigation bar to change language at any time. Your preference is saved to your account if you are logged in.

---

## Admin Panel

The admin panel is available at `/admin` and is accessible only to users with the `admin` role.

### Dashboard

The admin dashboard shows:
- Total revenue (current month / all time)
- New orders and pending quotations
- Pending B2B user approvals
- Open support tickets
- Recent activity feed

### Products

**Manage the product catalogue:**
- Create, edit, and delete products
- Add product images, variants, and pricing tiers
- Set translatable names and descriptions (Catalan, Spanish, English)
- Bulk import products via Excel upload
- Use the **AI Generate** tool to auto-generate product descriptions and SEO metadata

### Categories & Brands

- Create and manage hierarchical product categories
- Manage brands with translatable names

### Orders

- View all orders with filters (status, date, user)
- Update order status (pending → processing → shipped → completed)
- Export orders to Excel

### Quotations

- View incoming quotation requests
- Create itemised quotations and send pricing to customers
- Accept/reject quotation requests

### Users

- View all registered users
- Approve or reject pending B2B accounts
- View company associations

### Print Templates

- Create and manage print product templates
- Define customisation options, option values, and compatibility rules
- Set volume pricing tiers
- Track and manage print jobs by production status

### Reports

| Report | Contents |
|---|---|
| Revenue | Monthly revenue charts and totals |
| Products | Best-selling products and inventory levels |
| Clients | Customer activity and B2B statistics |
| Print Jobs | Print job volume and production metrics |

### Promo Codes

- Create discount codes (percentage or fixed amount)
- Set validity dates and usage limits

### Reviews

- View and moderate product reviews
- Remove inappropriate reviews

### Tickets

- Manage all customer support tickets
- Reply to tickets; customer is notified by email

### Contact Messages

- View all contact form submissions
- Mark as read / archive

### Notifications

The bell icon in the admin header shows real-time notifications for:
- New orders placed
- New quotation requests
- New B2B registration (pending approval)
- New support tickets
- Low stock alerts
