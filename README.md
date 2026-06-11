# Scott Pete — Child Theme

**Brand:** Scott Pete (SFG / Indiana Packers Corporation)  
**Parent theme:** `ipc-base`  
**Child theme slug:** `scott-pete`  
**Built by:** Overtime Agency

---

## Brand Tokens

| Token | Value | Usage |
|---|---|---|
| Primary (Navy) | `#152e86` | Nav, section BGs, headings |
| Secondary (Red) | `#c8102e` | Product section BG, accents |
| Accent (Gold/Peach) | `#fcc278` | CTAs, headings on dark BG, footer BG |

---

## Staging Setup Checklist

### 1. WordPress Multisite
- [ ] Add new site: `scottpete.{network-domain}`
- [ ] Set site title: **Scott Pete**
- [ ] Network-activate `ipc-base` parent theme (if not already)
- [ ] Install & activate `scott-pete` child theme on this site

### 2. Plugins
- [ ] ACF Pro — network active
- [ ] Classic Editor (if used) — site-level
- [ ] Destini (Where to Buy) — site-level

### 3. ACF Import
- [ ] Import `ipc-acf-field-groups.json` under **Custom Fields → Tools → Import**
- [ ] Verify all 15 field groups are present

### 4. ACF Options Pages — Configuration

#### Brand Options
- [ ] Logo: Upload Scott Pete 100-year badge (circular mark)
- [ ] Primary color: `#152e86`
- [ ] Secondary color: `#c8102e`
- [ ] Accent color: `#fcc278`
- [ ] Adobe Fonts Kit ID: *(obtain from Adobe Fonts dashboard)*

#### Header Options
- [ ] Nav links: Products | Recipes | Pete's Perks | History
- [ ] CTA button label: **Find Scott Pete's Near You!**
- [ ] CTA button URL: *(Destini embed page URL)*
- [ ] Header style: Transparent (overlays hero)

#### Footer Options
- [ ] Footer links: Where to Buy | Loyalty Club | Contact Us | Careers at SFG | Industry Solutions
- [ ] Copyright text: `Copyright © 2025 SFG, LLC and its Affiliated Companies. All Rights Reserved.`
- [ ] Privacy Policy link
- [ ] Do Not Sell My Personal Information link

#### Social Links (for Two-Col Banner block)
- [ ] Facebook URL
- [ ] YouTube URL
- [ ] Email/newsletter URL
- [ ] Other platform URLs

### 5. Taxonomy Terms

#### Product Categories
- [ ] Sausage
- [ ] Bologna
- [ ] Hot Dogs
- [ ] Lunch Meat
- [ ] Braunschweiger

#### Recipe Categories
- [ ] Bun-Wrecking Recipes
- [ ] Give the Bun a Break

### 6. Products (CPT)
Create one product post per SKU family. For each:
- [ ] Featured image (package photo)
- [ ] Product category (taxonomy)
- [ ] Description
- [ ] Variations (if applicable) — stored as JSON in `_product_variations`
- [ ] Destini SKUs (newline-separated in post meta)
- [ ] Certifications (taxonomy terms + badge images)

**Initial products:**
- [ ] Sausage (multiple varieties)
- [ ] Bologna
- [ ] Hot Dogs
- [ ] Lunch Meat
- [ ] Braunschweiger

### 7. Recipes (CPT)
- [ ] Create recipe posts with full meta (prep/cook times, ingredients, steps)
- [ ] Assign to appropriate recipe categories
- [ ] Add featured images

### 8. Home Page
Build using ACF Gutenberg blocks in this order:

| Order | Block | Notes |
|---|---|---|
| 1 | `home-banner` | Hero with logo, tagline, Find Near You CTA. BG: vintage photo |
| 2 | `two-col-banner` | "100 Years of Breaking Buns" — left: big heading, right: body copy + social icons + Our History button |
| 3 | `promo-slider` / flavor strip | "A Flavor For Every Fan" — hotdog hero image + package scroll row |
| 4 | `brand-container` | "More Products To Explore" — red BG, product grid, All Products button |
| 5 | `recipes-cta` | "Recipe Decision Tree" — two photo panels + center logo + All Recipes button |
| 6 | `join-form` | "Pete's Perks" — navy BG, city skyline image, 2-col email signup form |

### 9. Pages
- [ ] **Where to Buy** — Destini embed page
- [ ] **Pete's Perks** — standalone loyalty/perks page (or redirect to join form section)
- [ ] **History** — brand story page
- [ ] **Contact Us**
- [ ] **Products** archive (auto from CPT)
- [ ] **Recipes** archive (auto from CPT)

### 10. Menus
- [ ] Assign primary nav menu to **Primary Navigation** location
- [ ] Assign footer menu to **Footer Navigation** location

### 11. Settings
- [ ] Reading: Set Home Page to static **Home** page
- [ ] Permalinks: Post name (`/%postname%/`)
- [ ] Tagline: Celebrating 100 Years

---

## Asset Exports Needed from PSD

| Asset | Dimensions | Format | Notes |
|---|---|---|---|
| Scott Pete 100-year badge logo | SVG or 600px wide PNG | SVG preferred | Transparent BG |
| Hero background photo | 2400px wide | JPG | Vintage B&W kitchen/factory photo |
| Hotdog hero strip image | 1600px wide | PNG or JPG | For "A Flavor For Every Fan" |
| Product package images (5) | 600×600px | PNG | Transparent BG |
| Recipe photo — Bun-Wrecking | 600×600px | JPG | |
| Recipe photo — Give the Bun a Break | 600×600px | JPG | |
| Pete's Perks city skyline | 1600px wide | JPG | Chicago skyline |
| Product package images for footer stack | 400px wide each | PNG | Transparent BG |

---

## Notes

- **Recipe Decision Tree** uses a CSS modifier (`.recipe-tree-layout`) on the `recipes-cta` block. The tilted Polaroid-style photo treatment is handled with `transform: rotate()` in `style.css`.
- **Pete's Perks form** is a two-column grid; the parent `join-form` block may need a style variant class `perks-two-col` added in the block template.
- **Header** is transparent on the hero, then transitions to solid navy on scroll. This requires the scroll listener already in `ipc-base` JS — verify `header.scrolled` class is toggled correctly.
- **Adobe Fonts Kit ID** must be added before fonts will render. Set via ACF Brand Options → Adobe Fonts Kit ID field, or hardcode in `functions.php` as a temporary measure.
