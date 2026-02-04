{{-- Global preloader overlay partial. --}}
<div id="global-preloader">
    <div class="preloader-content">
        <img src="{{ asset('images/logo.png') }}" alt="Loading..." class="preloader-logo">
    </div>
</div>

<style>
    /* Global Preloader */
    #global-preloader {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: #ffffff;
        z-index: 99999; /* Above everything */
        display: flex;
        justify-content: center;
        align-items: center;
        transition: opacity 0.5s ease-out, visibility 0.5s ease-out;
    }

    #global-preloader.hidden {
        opacity: 0;
        visibility: hidden;
    }

    .preloader-logo {
        width: 80px;
        height: auto;
        animation: pulse-logo 2s infinite ease-in-out;
    }

    @keyframes pulse-logo {
        0% { transform: scale(1); opacity: 1; }
        50% { transform: scale(1.1); opacity: 0.8; }
        100% { transform: scale(1); opacity: 1; }
    }

    /* Button Spinner */
    .btn-loading {
        position: relative;
        color: transparent !important;
        pointer-events: none;
    }

    .btn-loading::after {
        content: "";
        position: absolute;
        left: 50%;
        top: 50%;
        width: 1.2em;
        height: 1.2em;
        border: 0.2em solid currentColor;
        border-right-color: transparent;
        border-radius: 50%;
        transform: translate(-50%, -50%);
        animation: btn-spin 0.6s linear infinite;
        color: white; /* Default spinner color */
    }
    
    .btn-outline-primary.btn-loading::after, 
    .btn-outline-secondary.btn-loading::after,
    .btn-light.btn-loading::after {
        color: #555; /* Dark spinner for light buttons */
    }

    @keyframes btn-spin {
        from { transform: translate(-50%, -50%) rotate(0deg); }
        to { transform: translate(-50%, -50%) rotate(360deg); }
    }
</style>

<script>
    (function(){
        // 1. Hide preloader on page load
        window.addEventListener('load', function() {
            const loader = document.getElementById('global-preloader');
            if (loader) {
                setTimeout(() => {
                    loader.classList.add('hidden');
                }, 300); // slight delay for smoothness
            }
        });

        // 2. Show spinner on buttons/links when clicked
        function showSpinner(el) {
            // If it's a link opening in new tab, ignore
            if (el.tagName === 'A' && el.target === '_blank') return;
            
            // Add loading class
            el.classList.add('btn-loading');
            
            // If it's a button, maybe disable it to prevent double submit (optional, but good practice)
            // if (el.tagName === 'BUTTON') el.disabled = true; 
        }

        // Delegate event listeners
        document.addEventListener('click', function(e) {
            const target = e.target.closest('a, button, input[type="submit"]');
            if (!target) return;

            // Ignore if it's a modal trigger or dropdown toggle or javascript:void
            if (target.getAttribute('href')?.startsWith('#') || 
                target.getAttribute('href')?.startsWith('javascript') ||
                target.hasAttribute('data-bs-toggle') ||
                target.hasAttribute('data-bs-dismiss')) {
                return;
            }

            // For forms, we handle in 'submit' event usually, but input[submit] is clicked
            if (target.tagName === 'INPUT' && target.type === 'submit') {
                // handled by form submit
                return;
            }

            // If it's a regular link
            if (target.tagName === 'A') {
                 // Check if same page anchor or real navigation
                 const href = target.getAttribute('href');
                 if (href && !href.startsWith('#') && !href.startsWith('javascript:')) {
                     showSpinner(target);
                 }
            }

            // If it's a button that submits a form, we let the form submit handler do it
            // But if it's a regular button acting as a link (rare but possible)
            if (target.tagName === 'BUTTON' && target.type !== 'submit') {
                 // Check if it navigates? usually handled by JS. 
                 // If it's just a JS action, we might not want a spinner unless requested.
                 // For now, apply mostly to submits and anchors.
            }
        });

        document.addEventListener('submit', function(e) {
            const form = e.target;
            // Find the submit button that triggered it
            // Active element might be the button
            const btn = document.activeElement;
            if (btn && (btn.type === 'submit' || btn.tagName === 'BUTTON') && form.contains(btn)) {
                showSpinner(btn);
            } else {
                // Fallback: find the first submit button
                const submitBtn = form.querySelector('[type="submit"], button:not([type="button"])');
                if (submitBtn) showSpinner(submitBtn);
            }
            
            // Also, if the user wanted the preloader to appear *immediately* on submit (covering screen),
            // uncomment the line below. But they preferred "cycling circle ... until redirected".
            // document.getElementById('global-preloader').classList.remove('hidden');
        });

        // Restore pages on back/forward cache (bfcache)
        window.addEventListener('pageshow', function(event) {
            if (event.persisted) {
                const loader = document.getElementById('global-preloader');
                if (loader) loader.classList.add('hidden');
                
                // Remove spinners
                document.querySelectorAll('.btn-loading').forEach(el => el.classList.remove('btn-loading'));
            }
        });
    })();
</script>

