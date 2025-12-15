export const Ziggy {
    "url": "https:\/\/feminaluxe",
    "port": null,
    "defaults": [],
    "routes": {
        "filament.exports.download": {
            "uri": "filament\/exports\/{export}\/download",
            "methods": [
                "GET",
                "HEAD"
            ],
            "parameters": [
                "export"
            ],
            "bindings": {
                "export": "id"
            }
        },
        "filament.imports.failed-rows.download": {
            "uri": "filament\/imports\/{import}\/failed-rows\/download",
            "methods": [
                "GET",
                "HEAD"
            ],
            "parameters": [
                "import"
            ],
            "bindings": {
                "import": "id"
            }
        },
        "filament.admin.auth.login": {
            "uri": "admin\/login",
            "methods": [
                "GET",
                "HEAD"
            ]
        },
        "filament.admin.auth.logout": {
            "uri": "admin\/logout",
            "methods": [
                "POST"
            ]
        },
        "filament.admin.pages.dashboard": {
            "uri": "admin",
            "methods": [
                "GET",
                "HEAD"
            ]
        },
        "filament.admin.resources.bookings.index": {
            "uri": "admin\/bookings",
            "methods": [
                "GET",
                "HEAD"
            ]
        },
        "filament.admin.resources.bookings.create": {
            "uri": "admin\/bookings\/create",
            "methods": [
                "GET",
                "HEAD"
            ]
        },
        "filament.admin.resources.bookings.view": {
            "uri": "admin\/bookings\/{record}",
            "methods": [
                "GET",
                "HEAD"
            ],
            "parameters": [
                "record"
            ]
        },
        "filament.admin.resources.bookings.edit": {
            "uri": "admin\/bookings\/{record}\/edit",
            "methods": [
                "GET",
                "HEAD"
            ],
            "parameters": [
                "record"
            ]
        },
        "filament.admin.resources.posts.index": {
            "uri": "admin\/posts",
            "methods": [
                "GET",
                "HEAD"
            ]
        },
        "filament.admin.resources.posts.create": {
            "uri": "admin\/posts\/create",
            "methods": [
                "GET",
                "HEAD"
            ]
        },
        "filament.admin.resources.posts.edit": {
            "uri": "admin\/posts\/{record}\/edit",
            "methods": [
                "GET",
                "HEAD"
            ],
            "parameters": [
                "record"
            ]
        },
        "filament.admin.resources.reviews.index": {
            "uri": "admin\/reviews",
            "methods": [
                "GET",
                "HEAD"
            ]
        },
        "filament.admin.resources.reviews.create": {
            "uri": "admin\/reviews\/create",
            "methods": [
                "GET",
                "HEAD"
            ]
        },
        "filament.admin.resources.reviews.edit": {
            "uri": "admin\/reviews\/{record}\/edit",
            "methods": [
                "GET",
                "HEAD"
            ],
            "parameters": [
                "record"
            ]
        },
        "filament.admin.resources.services.index": {
            "uri": "admin\/services",
            "methods": [
                "GET",
                "HEAD"
            ]
        },
        "filament.admin.resources.services.create": {
            "uri": "admin\/services\/create",
            "methods": [
                "GET",
                "HEAD"
            ]
        },
        "filament.admin.resources.services.edit": {
            "uri": "admin\/services\/{record}\/edit",
            "methods": [
                "GET",
                "HEAD"
            ],
            "parameters": [
                "record"
            ]
        },
        "livewire.update": {
            "uri": "livewire\/update",
            "methods": [
                "POST"
            ]
        },
        "livewire.upload-file": {
            "uri": "livewire\/upload-file",
            "methods": [
                "POST"
            ]
        },
        "livewire.preview-file": {
            "uri": "livewire\/preview-file\/{filename}",
            "methods": [
                "GET",
                "HEAD"
            ],
            "parameters": [
                "filename"
            ]
        },
        "sitemap.status": {
            "uri": "api\/sitemap\/status",
            "methods": [
                "GET",
                "HEAD"
            ]
        },
        "sitemap.generate": {
            "uri": "api\/sitemap\/generate",
            "methods": [
                "POST"
            ]
        },
        "sitemap.clear-cache": {
            "uri": "api\/sitemap\/clear-cache",
            "methods": [
                "POST"
            ]
        },
        "sitemap.validate": {
            "uri": "api\/sitemap\/validate",
            "methods": [
                "POST"
            ]
        },
        "sitemap.submit": {
            "uri": "api\/sitemap\/submit",
            "methods": [
                "POST"
            ]
        },
        "sitemap.backup": {
            "uri": "api\/sitemap\/backup",
            "methods": [
                "POST"
            ]
        },
        "sitemap.settings.get": {
            "uri": "api\/sitemap\/settings",
            "methods": [
                "GET",
                "HEAD"
            ]
        },
        "sitemap.settings.update": {
            "uri": "api\/sitemap\/settings",
            "methods": [
                "PUT"
            ]
        },
        "sitemap.download-backup": {
            "uri": "api\/sitemap\/download-backup\/{filename}",
            "methods": [
                "GET",
                "HEAD"
            ],
            "parameters": [
                "filename"
            ]
        },
        "api.reviews-teaser": {
            "uri": "api\/reviews-teaser",
            "methods": [
                "GET",
                "HEAD"
            ]
        },
        "api.reviews": {
            "uri": "api\/reviews",
            "methods": [
                "GET",
                "HEAD"
            ]
        },
        "home": {
            "uri": "\/",
            "methods": [
                "GET",
                "HEAD"
            ]
        },
        "services.index": {
            "uri": "services",
            "methods": [
                "GET",
                "HEAD"
            ]
        },
        "services.filter": {
            "uri": "services\/filter",
            "methods": [
                "GET",
                "HEAD"
            ]
        },
        "services.popular": {
            "uri": "services\/popular",
            "methods": [
                "GET",
                "HEAD"
            ]
        },
        "services.category": {
            "uri": "services\/category\/{categorySlug}",
            "methods": [
                "GET",
                "HEAD"
            ],
            "parameters": [
                "categorySlug"
            ]
        },
        "services.show": {
            "uri": "services\/{slug}",
            "methods": [
                "GET",
                "HEAD"
            ],
            "parameters": [
                "slug"
            ]
        },
        "blog.index": {
            "uri": "blog",
            "methods": [
                "GET",
                "HEAD"
            ]
        },
        "blog.show": {
            "uri": "blog\/{post}",
            "methods": [
                "GET",
                "HEAD"
            ],
            "parameters": [
                "post"
            ],
            "bindings": {
                "post": "slug"
            }
        },
        "portfolio": {
            "uri": "portfolio",
            "methods": [
                "GET",
                "HEAD"
            ]
        },
        "about": {
            "uri": "about",
            "methods": [
                "GET",
                "HEAD"
            ]
        },
        "contact": {
            "uri": "contact",
            "methods": [
                "GET",
                "HEAD"
            ]
        },
        "bookings.store": {
            "uri": "bookings",
            "methods": [
                "POST"
            ]
        },
        "sitemap": {
            "uri": "sitemap.xml",
            "methods": [
                "GET",
                "HEAD"
            ]
        },
        "robots": {
            "uri": "robots.txt",
            "methods": [
                "GET",
                "HEAD"
            ]
        },
        "storage.local": {
            "uri": "storage\/{path}",
            "methods": [
                "GET",
                "HEAD"
            ],
            "wheres": {
                "path": ".*"
            },
            "parameters": [
                "path"
            ]
        }
    }
};
