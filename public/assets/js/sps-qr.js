/**
 * SPS Authentic QR Code Generator with Center Brand Logo
 * Uses ISO/IEC 18004 Standard with Level H Error Correction (30% Recovery)
 */
function renderSpsQrCode(targetImgId, textUrl, logoUrl) {
    const targetImg = document.getElementById(targetImgId);
    if (!targetImg) return;

    if (typeof QRCode === 'undefined') {
        console.warn('QRCode library not loaded yet');
        return;
    }

    const tempDiv = document.createElement('div');
    tempDiv.style.display = 'none';
    document.body.appendChild(tempDiv);

    try {
        new QRCode(tempDiv, {
            text: textUrl,
            width: 256,
            height: 256,
            colorDark: "#0f172a",
            colorLight: "#ffffff",
            correctLevel: QRCode.CorrectLevel.H
        });

        // Polling briefly to let QRCode canvas finish rendering
        let attempts = 0;
        const checkInterval = setInterval(function() {
            attempts++;
            let sourceCanvas = tempDiv.querySelector('canvas');
            if (!sourceCanvas) {
                const sourceImg = tempDiv.querySelector('img');
                if (sourceImg && sourceImg.src && sourceImg.src.startsWith('data:')) {
                    sourceCanvas = document.createElement('canvas');
                    sourceCanvas.width = 256;
                    sourceCanvas.height = 256;
                    const c = sourceCanvas.getContext('2d');
                    c.drawImage(sourceImg, 0, 0, 256, 256);
                }
            }

            if (sourceCanvas || attempts > 20) {
                clearInterval(checkInterval);
                if (sourceCanvas) {
                    compositeQrWithLogo(sourceCanvas, targetImg, logoUrl);
                }
                tempDiv.remove();
            }
        }, 30);
    } catch (e) {
        console.error('Failed to generate QR code:', e);
        tempDiv.remove();
    }
}

function compositeQrWithLogo(sourceCanvas, targetImg, logoUrl) {
    const finalCanvas = document.createElement('canvas');
    finalCanvas.width = 256;
    finalCanvas.height = 256;
    const ctx = finalCanvas.getContext('2d');

    // 1. Draw base high-resolution QR pattern
    ctx.drawImage(sourceCanvas, 0, 0, 256, 256);

    // 2. Load and overlay SPS Logo in center
    const logo = new Image();
    logo.crossOrigin = "anonymous";
    logo.src = logoUrl;

    const drawCenterBadge = function(imgLoaded) {
        const logoSize = 52; // ~20% of width, safe within 30% Level H tolerance
        const pos = (256 - logoSize) / 2;
        const pad = 6;
        const badgeX = pos - pad;
        const badgeY = pos - pad;
        const badgeW = logoSize + (pad * 2);
        const badgeH = logoSize + (pad * 2);

        // White background card for clear contrast & protection
        ctx.fillStyle = "#ffffff";
        ctx.beginPath();
        if (ctx.roundRect) {
            ctx.roundRect(badgeX, badgeY, badgeW, badgeH, 8);
        } else {
            ctx.rect(badgeX, badgeY, badgeW, badgeH);
        }
        ctx.fill();

        // Elegant border for center badge
        ctx.strokeStyle = "#cbd5e1";
        ctx.lineWidth = 2;
        ctx.stroke();

        if (imgLoaded) {
            ctx.drawImage(logo, pos, pos, logoSize, logoSize);
        } else {
            ctx.fillStyle = "#c65a1e";
            ctx.font = "bold 16px sans-serif";
            ctx.textAlign = "center";
            ctx.textBaseline = "middle";
            ctx.fillText("SPS", 128, 128);
        }

        // Apply PNG data URI to target image
        targetImg.src = finalCanvas.toDataURL("image/png");
    };

    logo.onload = function() {
        drawCenterBadge(true);
    };
    logo.onerror = function() {
        drawCenterBadge(false);
    };
}
