# Expandability | ما يجب أن يكون قابلًا للتوسع

Brief V3 labels this **§16** (some external notes number it 17). Ownership/contract is Brief **§17**.

## Non-negotiable rule

**Forbidden:** hard-coding products / packages / paths / services in views or controllers so every small catalogue change needs a developer.

**Required:** admin-managed catalogue + modular architecture that can grow without rewriting the core loop.

Runtime registry: `config/expandability.php` + `App\Support\Expandability`.

| Capability | Status | Admin entry |
|------------|--------|-------------|
| مسارات جديدة | works | `admin.learning-paths.*` |
| وحدات / دروس / أدوات | works | path nested CRUD + `admin.teacher-tools.*` |
| منتجات وخدمات | works | packages + consultation services + tools (separate tables) |
| باقات وأسعار | works | `admin.packages.*` |
| أنواع استشارات | partial | services CRUD; **type keys** in `platform.consultations.types` |
| برامج تدريب مدارس/مؤسسات | works | `admin.institution-programs.*` (`kind=training`) |
| مدارس/مؤسسات + مشاركين | works | `admin.institutions.*` + participants |
| قنوات إشعارات | partial | WhatsApp/Email live; SMS/Push stubs; new channel = adapter + config |
| تغيير Payment Gateway | works* | `admin.payment-gateways.*` (toggle existing adapters) |
| توسيع أدوار/صلاحيات | partial | DB roles/permissions; `rbac_*` maps for new UI wiring |

\*New payment **provider brand** still needs an adapter class — credentials/enablement of built-in gateways do not.

## What may stay in config (taxonomy, not catalogue rows)

OK in `config/platform.php` / `config/notifications.php`:
- Type **keys** and labels (consultation types, package types, tool categories)
- Module flags, journey/expandability maps
- Default notification templates (overridable in admin)

**Not OK:** listing sellable path/package/service **instances** in Blade or PHP arrays as the live catalogue.

## Agent rules

1. New catalogue content → admin CRUD + DB, never a new hard-coded list in a public Blade.
2. New taxonomy key (e.g. consultation type) → add to `config/platform.php` and keep validation driven from that config — do not duplicate enums in models.
3. Update `config/expandability.php` status when a capability moves works ↔ partial.
4. Do not build CMS for marketing lang copy unless requested; that is separate from catalogue expandability.
