// resources/js/utils/analytics.js
const isDevelopment = import.meta.env.DEV;
const isProduction = import.meta.env.PROD;

export const Analytics = {
    // Отслеживание событий
    trackEvent(eventName, eventParams = {}) {
        if (isDevelopment) {
            console.log(`🔔 [Analytics Dev] ${eventName}:`, eventParams);
            return;
        }
        
        if (isProduction) {
            // Google Analytics 4
            if (window.gtag && typeof window.gtag === 'function') {
                window.gtag('event', eventName, {
                    ...eventParams,
                    send_to: window.GOOGLE_ANALYTICS_ID
                });
            }

            // Яндекс.Метрика
            if (window.ym && typeof window.ym === 'function') {
                window.ym(window.YANDEX_METRIKA_ID, 'reachGoal', eventName, eventParams);
            }

            // Facebook Pixel
            if (window.fbq && typeof window.fbq === 'function') {
                window.fbq('track', eventName, eventParams);
            }
        }
    },
    
    // Отслеживание бронирования
    trackBooking(booking) {
        this.trackEvent('booking_success', {
            transaction_id: booking.id,
            value: booking.price,
            currency: 'RUB',
            items: [{
                item_id: booking.service_id,
                item_name: booking.service_name,
                price: booking.price,
                quantity: 1
            }]
        });
        
        // Enhanced E-commerce для GA4
        if (window.gtag) {
            window.gtag('event', 'purchase', {
                transaction_id: booking.id,
                value: booking.price,
                currency: 'RUB',
                items: [{
                    item_id: booking.service_id,
                    item_name: booking.service_name,
                    price: booking.price,
                    quantity: 1
                }]
            });
        }
    },
    
    // Просмотр услуги
    trackServiceView(service) {
        this.trackEvent('view_service', {
            service_id: service.id,
            service_name: service.name,
            category: service.category,
            price: service.price
        });
        
        // Enhanced E-commerce для GA4
        if (window.gtag) {
            window.gtag('event', 'view_item', {
                items: [{
                    item_id: service.id,
                    item_name: service.name,
                    category: service.category,
                    price: service.price
                }]
            });
        }
    },
    
    // Клик по телефону
    trackPhoneClick(phoneNumber) {
        this.trackEvent('phone_click', {
            phone: phoneNumber,
            page: window.location.pathname
        });
    },
    
    // Отправка формы
    trackFormSubmit(formName, formData = {}) {
        this.trackEvent('form_submit', {
            form_name: formName,
            ...formData
        });
    },
    
    // Просмотр портфолио
    trackPortfolioView(portfolioId) {
        this.trackEvent('portfolio_view', {
            portfolio_id: portfolioId
        });
    },
    
    // Действия в блоге
    trackBlogAction(action, postId) {
        this.trackEvent(`blog_${action}`, {
            post_id: postId,
            post_title: document.title
        });
    }
};