(function() {
    'use strict';

    // Get settings from WordPress
    const settings = typeof plantGrowSettings !== 'undefined' ? plantGrowSettings.settings : {};
    const stageImages = typeof plantGrowSettings !== 'undefined' ? plantGrowSettings.stages : {};

    // Apply custom styles from settings
    function applyCustomStyles() {
        const widget = document.getElementById('plant-widget');
        if (!widget) return;

        // Apply position and size
        if (settings.bottom !== undefined) {
            widget.style.bottom = settings.bottom + 'px';
        }
        if (settings.left !== undefined) {
            widget.style.left = settings.left + 'px';
        }
        if (settings.width !== undefined) {
            widget.style.width = settings.width + 'px';
        }

        // Apply progress bar color
        const progressBar = document.getElementById('progressBar');
        if (progressBar && settings.progressBarColor) {
            progressBar.style.background = 'linear-gradient(to right, ' + settings.progressBarColor + ', ' + settings.progressBarColor + ')';
        }

        // Apply progress text styles
        const progressText = document.getElementById('progressText');
        if (progressText) {
            if (settings.progressTextColor) {
                progressText.style.color = settings.progressTextColor;
            }
            if (settings.progressTextSize) {
                progressText.style.fontSize = settings.progressTextSize + 'px';
            }
        }

        // Apply mobile background
        if (settings.mobileBackground) {
            const style = document.createElement('style');
            style.textContent = '@media (max-width: 768px) { .plant-widget { background: ' + settings.mobileBackground + ' !important; } }';
            document.head.appendChild(style);
        }

        // Update plant stage widths to match widget width
        const plantStages = document.querySelectorAll('.plant-stage');
        if (settings.width) {
            plantStages.forEach(function(stage) {
                stage.style.width = settings.width + 'px';
            });
        }
    }

    // Preload all plant stage images for smooth transitions
    function preloadImages() {
        if (!stageImages || Object.keys(stageImages).length === 0) return;

        Object.keys(stageImages).forEach(function(stage) {
            const img = new Image();
            img.src = stageImages[stage];
        });
    }

    // Track scroll progress and plant growth
    let maxScrollReached = 0;
    let currentStage = 0;

    // Reset function to initialize plant state on page load/refresh
    function resetPlantGrowth() {
        maxScrollReached = 0;
        currentStage = 0;

        const progressBar = document.getElementById('progressBar');
        const progressText = document.getElementById('progressText');
        
        if (progressBar) {
            progressBar.style.width = '0%';
        }
        if (progressText) {
            progressText.textContent = '0%';
        }

        // Remove active class from all stages
        for (let i = 1; i <= 14; i++) {
            const stage = document.getElementById('stage' + i);
            if (stage) {
                stage.classList.remove('active');
            }
        }

        // Show only stage 1 (seeds)
        const stage1 = document.getElementById('stage1');
        if (stage1) {
            stage1.classList.add('active');
        }

        const progressSection = document.getElementById('progressSection');
        if (progressSection) {
            progressSection.classList.remove('hidden');
        }
    }

    function updatePlantGrowth() {
        // Calculate scroll percentage
        const windowHeight = window.innerHeight;
        const documentHeight = document.documentElement.scrollHeight;
        const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
        const scrollableHeight = documentHeight - windowHeight;
        const scrollPercentage = scrollableHeight > 0 ? Math.min(100, Math.max(0, (scrollTop / scrollableHeight) * 100)) : 0;

        // Update max scroll reached (plant never shrinks)
        if (scrollPercentage > maxScrollReached) {
            maxScrollReached = scrollPercentage;
        }

        // Update progress bar
        const progressBar = document.getElementById('progressBar');
        const progressText = document.getElementById('progressText');
        
        if (progressBar) {
            progressBar.style.width = maxScrollReached + '%';
        }
        if (progressText) {
            progressText.textContent = Math.round(maxScrollReached) + '%';
        }

        // Determine which stage should be active based on max scroll reached (14 stages)
        let newStage = 0;
        if (maxScrollReached >= 92) {
            newStage = 14;
        } else if (maxScrollReached >= 85) {
            newStage = 13;
        } else if (maxScrollReached >= 78) {
            newStage = 12;
        } else if (maxScrollReached >= 71) {
            newStage = 11;
        } else if (maxScrollReached >= 64) {
            newStage = 10;
        } else if (maxScrollReached >= 57) {
            newStage = 9;
        } else if (maxScrollReached >= 50) {
            newStage = 8;
        } else if (maxScrollReached >= 43) {
            newStage = 7;
        } else if (maxScrollReached >= 36) {
            newStage = 6;
        } else if (maxScrollReached >= 29) {
            newStage = 5;
        } else if (maxScrollReached >= 22) {
            newStage = 4;
        } else if (maxScrollReached >= 15) {
            newStage = 3;
        } else if (maxScrollReached >= 8) {
            newStage = 2;
        } else {
            newStage = 1;
        }

        // Update stage if it's higher than current
        if (newStage > currentStage) {
            // Remove active class from all stages
            for (let i = 1; i <= 14; i++) {
                const stage = document.getElementById('stage' + i);
                if (stage) {
                    stage.classList.remove('active');
                }
            }
            
            // Add active class to new stage
            currentStage = newStage;
            const newStageElement = document.getElementById('stage' + currentStage);
            if (newStageElement) {
                newStageElement.classList.add('active');
            }
        }

        // Progress section always visible
        const progressSection = document.getElementById('progressSection');
        if (progressSection) {
            progressSection.classList.remove('hidden');
        }
    }

    // Initialize when DOM is ready
    function init() {
        applyCustomStyles();
        preloadImages();
        resetPlantGrowth();
        updatePlantGrowth();
        
        // Listen for scroll events
        window.addEventListener('scroll', updatePlantGrowth);
        window.addEventListener('resize', updatePlantGrowth);
    }

    // Run when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();

