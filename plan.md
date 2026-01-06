
# INTERNATIONAL AGRO MARKETPLACE (IAM)

**MVP Blueprint – Subscription-Based Multi-Vendor Agriculture Platform**

Inspired by: **AgriXchange.com**

---

## 1. INTRODUCTION (MVP SCOPE)

International Agro Marketplace (IAM) is a **subscription-based, multi-vendor agriculture e-commerce platform** designed exclusively for agricultural trade.

The MVP focuses on **connecting verified agricultural sellers (farmers, traders, cooperatives, companies)** with **bulk buyers** through a simple, trusted, and subscription-driven marketplace.

This is **not retail**, and not a general marketplace.

The MVP prioritizes:

* Fast onboarding
* Subscription access (monthly / weekly)
* Bulk agricultural listings
* Simple negotiation (no complex procurement systems)
* Trust through basic verification

---

## 2. BUSINESS MODEL (SUBSCRIPTION-FIRST)

### 2.1 Revenue Model (MVP)

**Primary Revenue**

* **Seller subscriptions**

  * Weekly plan
  * Monthly plan
* **Buyer subscriptions**

  * Access to seller contacts
  * RFQ submissions
  * Order placement

**No transaction commissions in MVP**
(keeps system simple and adoption high)

---

### 2.2 Subscription Plans (Example)

**Sellers**

* Weekly subscription (basic listing access)
* Monthly subscription (priority listing + messaging)

**Buyers**

* Monthly subscription (browse, RFQ, contact sellers)

> Subscriptions control access — not payments per transaction.

---

## 3. PLATFORM USERS (MULTI-VENDOR)

### 3.1 User Types

1. **Farmers**
2. **Traders / Aggregators**
3. **Agri Companies / Processors**
4. **Buyers (bulk purchasers)**
5. **Admin**

Each seller type registers using **different forms**, but shares the same marketplace.

---

## 4. REGISTRATION & ONBOARDING (FAST & SIMPLE)

### 4.1 Seller Registration Forms (MVP)

Separate registration paths for:

* user
* Vendor(Admin)
* Super Admin

**Required Fields (MVP only)**

* Full name / Business name
* Phone number
* Email
* Location
* Type of products sold
* Business type (farmer / trader / company)

**Optional (post-registration)**

* Upload documents
* Certifications
* Company registration

> No hard KYC blocking in MVP — verification is **progressive**.

---

### 4.2 Buyer Registration

* Organization name
* Contact person
* Email & phone
* Buyer type (local / regional / international)

---

## 5. CORE FEATURES (MVP – QUICK TO BUILD)

### 5.1 Product Listings (Bulk Only)

Each seller can list products with:

* Product category
* Product name
* Quantity available
* Unit (kg, tonnes, bags, heads, etc.)
* Minimum order quantity
* Location
* Price (optional or “on request”)
* Short description

**Categories (MVP)**

* Grains & cereals
* Flour
* Vegetables
* Livestock
* Flowers

---

### 5.2 Subscription Gating

* Users **must have an active subscription** to:

  * View seller contact details
  * Send messages
  * Submit RFQs
  * Post listings

No subscription → limited browsing only.

---

### 5.3 Messaging (Simple)

* Buyer ↔ Seller direct messaging
* No attachments in MVP
* Message history stored per conversation

---

### 5.4 RFQ (Simplified MVP)

Instead of complex workflows:

* Buyer clicks **“Request Quote”**
* Enters:

  * Quantity
  * Delivery location
  * Preferred date
* Seller responds via message

No automated contracts in MVP.

---

### 5.5 Admin Panel (Lean)

Admin can:

* View users
* Approve / suspend accounts
* Moderate listings
* Manage subscriptions
* View basic stats

---

## 6. NON-FUNCTIONAL REQUIREMENTS (MVP LEVEL)

### 6.1 Security

* Password hashing
* Basic RBAC
* Secure APIs

### 6.2 Performance

* Optimized for low to medium traffic
* Cloud hosting

### 6.3 Scalability

* Modular architecture
* Subscription logic isolated

---

## 7. SYSTEM ARCHITECTURE (MVP)

### 7.1 High-Level Architecture

* Web frontend
* Backend API
* Database
* File storage (documents)
* Payment gateway (subscriptions only)

---

### 7.2 Core Entities (MVP)

* User
* SellerProfile
* BuyerProfile
* Subscription
* Product
* Message
* RFQ
* Payment
* AdminAction

---

## 8. DEVELOPMENT PHASES (MVP TIMELINE)

### Phase 1 – Foundation (2–3 weeks)

* Auth & roles
* Seller & buyer registration
* Subscription logic
* Basic admin panel

### Phase 2 – Marketplace (2–3 weeks)

* Product listings
* Browse & search
* Messaging
* RFQ

### Phase 3 – Payments & Launch (1–2 weeks)

* Subscription payments
* Access control
* Testing
* Deployment

---

## 9. WHAT IS INTENTIONALLY EXCLUDED (FOR SPEED)

❌ Logistics integration
❌ Financing
❌ Automated contracts
❌ Advanced analytics
❌ Heavy KYC enforcement
❌ APIs for third parties

These come **after validation**.

---

## 10. FUTURE ROADMAP (POST-MVP)

* Verified badges
* Escrow payments
* Logistics partners
* Export documentation
* Mobile app
* API access

