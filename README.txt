# SAF Trade Ukraine

### Premium B2B Flower Wholesale WordPress Website

A custom WordPress website developed for **ТОВ «САФ ТРЕЙД УКРАЇНА» / LLC SAF TRADE UKRAINE**, focused on professional flower wholesale, B2B inquiries, catalogue management, and responsive customer experience.

🌐 **Live Website:** https://www.saftd.com.ua/

---

## ✨ Project Overview

SAF Trade Ukraine is a fully custom WordPress solution designed for florists, retailers, decorators, distributors, and other professional flower buyers.

The website combines a premium visual identity with a scalable flower catalogue, reusable product templates, business inquiry management, and SMTP-powered email notifications.

---

## 🚀 Key Features

- Custom responsive WordPress theme
- Premium B2B flower wholesale design
- Dynamic flower catalogue
- Category filtering and catalogue search
- Reusable flower/product templates
- Expandable product and category structure
- Individual flower detail pages
- Wholesale inquiry/request workflow
- Inquiry storage directly inside WordPress Admin
- SMTP-powered email notifications
- Custom About, Wholesale, Delivery and Contact pages
- Sourcing and flower-origin information
- Detailed logistics and delivery content
- FAQ and informational pages
- Responsive desktop, tablet and mobile layouts
- Ukrainian-language interface and content
- SEO-friendly semantic structure
- Custom navigation and footer
- Clean URL/permalink structure
- Comment system disabled on business/catalogue pages
- WordPress/Jetpack frontend cleanup
- Local image fallbacks and optimized visual presentation

---

## 🛠 Technology

| Technology | Purpose |
|---|---|
| WordPress | CMS and administration |
| PHP | Theme logic and backend functionality |
| HTML5 | Semantic page structure |
| CSS3 | Custom responsive UI |
| JavaScript | Interactive functionality |
| WordPress AJAX | Dynamic form interactions |
| WP Mail SMTP | Reliable email delivery |
| Custom Post Types | Flower catalogue and inquiries |
| Custom Taxonomies | Flower categories |

---

## 🎨 Brand System

| Color | Hex |
|---|---|
| Primary Dark Green | `#0B3327` |
| Deep Green | `#06241B` |
| Medium Green | `#3F765F` |
| Soft Green | `#6F9A86` |
| Warm Cream | `#F7F3EA` |
| Accent Burgundy | `#8A3F52` |

The design follows a clean, premium and professional B2B direction while maintaining strong visual consistency across the catalogue, company pages, forms and mobile experience.

---

## 🌸 Catalogue Architecture

The flower catalogue is built to be scalable.

Administrators can add additional flowers and categories without redesigning individual pages.

Each flower can use the same reusable structure for:

- Name
- Category
- Featured image
- Availability
- Origin/source information
- Packaging
- Color
- Length/specifications
- Minimum-order information
- B2B inquiry CTA

This makes the catalogue easy to expand as the business adds more products.

---

## 📩 B2B Inquiry System

Visitors can submit wholesale requests directly through the website.

The workflow is:

**Website Inquiry → WordPress Backend → Inquiry Storage → SMTP Notification → Administrator Email**

Requests are also stored inside:

**WordPress Admin → Оптові запити**

This allows website administrators to review and manage submitted B2B requests directly from WordPress.

---

## 📱 Responsive Design

The website has been tested and optimized for:

**Desktop • Laptop • Tablet • Mobile**

Responsive optimization includes:

- Navigation
- Hero sections
- Catalogue grids
- Product pages
- Typography
- Forms
- Buttons
- Informational pages
- Footer
- Spacing and alignment

---

## 🧩 Main Website Sections

The website includes:

- Homepage
- Flower Catalogue
- Individual Flower/Product Pages
- Flower Categories
- About the Company
- Wholesale Customers
- Delivery & Logistics
- Flower Sourcing & Origin
- Wholesale Ordering Information
- Freshness & Quality
- FAQ
- Contact / Inquiry Workflow

---

## 🔧 Installation

1. Clone or download this repository.
2. Compress the WordPress theme folder into a `.zip` file if required.
3. Open WordPress Admin.
4. Go to:

   `Appearance → Themes → Add New → Upload Theme`

5. Upload and activate the theme.
6. Go to:

   `Settings → Permalinks`

7. Click **Save Changes** once to refresh WordPress rewrite rules.
8. Configure SMTP separately for production email delivery.

---

## 📧 Email Configuration

Production email delivery is configured using **WP Mail SMTP**.

The website inquiry system can send notifications through authenticated SMTP.

SMTP configuration is intentionally excluded from the repository.

Typical configuration includes:

- SMTP host
- Encryption
- SMTP port
- SMTP username
- SMTP password
- From email
- From name

Never commit SMTP credentials to Git.

---

## 🔐 Security

Sensitive credentials are intentionally excluded from this repository.

This repository does **not** contain:

- WordPress administrator passwords
- Hosting credentials
- SMTP passwords
- Database credentials
- API keys
- Private tokens
- `wp-config.php`
- `.env` secrets
- Client login credentials

Production credentials should always be configured directly on the hosting environment.

---

## 🧹 Frontend Cleanup

The final website includes cleanup for unnecessary WordPress/Jetpack frontend elements.

This includes:

- Disabled comments on company pages
- Disabled comments on catalogue pages
- Disabled comments on flower/product pages
- Disabled comments on category/archive pages
- Removal of default German WordPress comment UI
- Removal of public backend/development wording
- Removal of admin-oriented placeholder text
- Clean customer-facing empty states
- No visible WordPress setup instructions

---

## 🌐 SEO & Structure

The theme uses a clean, search-friendly structure including:

- Semantic page layouts
- Clean permalink structure
- Structured headings
- SEO-ready page titles
- Meta descriptions
- Image alt text support
- Schema basics
- Mobile-responsive layout
- Fast local image loading

The website is technically prepared for standard WordPress SEO workflows.

---

## 🖼 Image Handling

The theme uses local image assets for important visual sections to avoid broken third-party image dependencies.

The catalogue includes premium flower photography for categories such as:

- Seasonal flowers
- Sunflowers
- Chrysanthemums
- Roses
- Tulips

The website also includes local fallback handling for missing images where required.

---

## 🧪 Testing & QA

The website has been tested for:

- Homepage loading
- Catalogue loading
- Product pages
- Flower categories
- Navigation links
- Internal links
- B2B inquiry modal
- Inquiry submission
- WordPress backend inquiry storage
- SMTP email delivery
- Desktop layout
- Tablet layout
- Mobile layout
- Image loading
- Permalink routing
- Comment removal
- Public frontend cleanup

---

## ✅ Production Status

**Production Ready**

The website has been deployed and tested on the client’s production hosting.

Verified production functionality includes:

- Website pages
- Catalogue
- Flower detail pages
- Responsive layout
- Inquiry forms
- WordPress inquiry storage
- SMTP email delivery
- Custom domain deployment

---

## 🚀 Deployment

The project has been deployed to:

**https://www.saftd.com.ua/**

Typical deployment workflow:

1. Install WordPress
2. Upload the custom theme
3. Activate the theme
4. Refresh permalink rules
5. Verify catalogue pages
6. Configure SMTP
7. Submit a test inquiry
8. Confirm inquiry storage
9. Confirm notification email delivery
10. Complete responsive QA

---

## 👨‍💻 Development Scope

The project includes custom work covering:

- WordPress theme development
- UI/UX implementation
- Responsive frontend development
- Flower catalogue architecture
- Reusable product templates
- Custom post types
- Custom taxonomies
- B2B inquiry functionality
- WordPress backend integration
- SMTP configuration
- Content structure
- Visual optimization
- Deployment
- Final QA

---

## 📂 Suggested Project Structure

```text
saf-trade-ukraine/
├── assets/
│   ├── css/
│   ├── js/
│   └── images/
├── template-parts/
├── functions.php
├── style.css
├── header.php
├── footer.php
├── front-page.php
├── archive.php
├── single.php
├── page.php
├── taxonomy.php
└── README.md
