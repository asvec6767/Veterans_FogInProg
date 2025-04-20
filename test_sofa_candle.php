<?php include_once('header.php'); ?>
<link rel="stylesheet" href="/css/candle.css">

<p>Зажженные свечи: <span id='candle-count'>12</span> <button id="light-candle-btn" class="toggle-candle-btn">Зажечь свечу</button></p>
<div class="background-container">
    <div class="container">
        <div class="candle-container">
            <div class="candle">
                <div class="candle-stick"></div>
                <div class="candle-body"></div>
                <div class="candle-flame"></div>
                <div class="wax-drips" id="waxDrips"></div>
            </div>
        </div>
    </div>
</div>



<script>
document.addEventListener('DOMContentLoaded', function() {
    const candle = document.querySelector('.candle');
    const backgroundContainer = document.querySelector('.background-container');
    const waxDrips = document.getElementById('waxDrips');
    const candleCount = document.getElementById('candle-count');
    const lightBtn = document.getElementById('light-candle-btn');
    let isLit = false;
    let dripInterval;

    // Initialize candle state
    function initCandle() {
        const count = parseInt(candleCount.textContent);
        if (count > 0) {
            backgroundContainer.style.opacity = '1';
            candle.classList.add('lit');
            isLit = true;
            lightBtn.textContent = 'Погасить свечу';
            startDrips();
        } else {
            backgroundContainer.style.opacity = '0';
            candle.classList.remove('lit');
            isLit = false;
            lightBtn.textContent = 'Зажечь свечу';
            stopDrips();
        }
    }

    // Start wax drips
    function startDrips() {
        stopDrips();
        dripInterval = setInterval(createDrip, 3000);
        // Create initial drips
        for (let i = 0; i < 3; i++) {
            setTimeout(createDrip, i * 1000);
        }
    }

    // Stop wax drips
    function stopDrips() {
        if (dripInterval) {
            clearInterval(dripInterval);
        }
    }

    // Create a single wax drip
    function createDrip() {
        if (!isLit) return;
        
        const drip = document.createElement('div');
        drip.classList.add('drip');
        
        // Random position along candle width
        const leftPos = Math.random() * 80 + 10;
        drip.style.left = `${leftPos}px`;
        
        // Random drip width
        const dripWidth = Math.random() * 5 + 5;
        drip.style.width = `${dripWidth}px`;
        
        waxDrips.appendChild(drip);
        
        // Remove drip after animation completes
        setTimeout(() => {
            if (drip.parentNode) {
                drip.remove();
            }
        }, 3000);
    }

    // Handle button click
    lightBtn.addEventListener('click', function() {
        const count = parseInt(candleCount.textContent);
        
        if (!isLit) {
            // Light the candle
            if (count <= 0) return;
            backgroundContainer.style.opacity = '1';
            candle.classList.add('lit');
            isLit = true;
            lightBtn.textContent = 'Погасить свечу';
            startDrips();
        } else {
            // Extinguish the candle
            backgroundContainer.style.opacity = '0';
            candle.classList.remove('lit');
            isLit = false;
            lightBtn.textContent = 'Зажечь свечу';
            stopDrips();
        }
    });

    // Initialize on load
    initCandle();
});
</script>

<?php include_once('footer.php'); ?>