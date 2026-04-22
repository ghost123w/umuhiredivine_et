let scene, camera, renderer, particles;
let mouseX = 0, mouseY = 0;
let targetX = 0, targetY = 0;

function init() {
    scene = new THREE.Scene();
    camera = new THREE.PerspectiveCamera(75, window.innerWidth / window.innerHeight, 0.1, 1000);
    camera.position.z = 5;

    renderer = new THREE.WebGLRenderer({ alpha: true, antialias: true });
    renderer.setSize(window.innerWidth, window.innerHeight);
    renderer.setPixelRatio(window.devicePixelRatio);
    const container = document.getElementById('canvas-container');
    if (container) {
        container.appendChild(renderer.domElement);
    }

    const geometry = new THREE.BufferGeometry();
    const vertices = [];
    const sizes = [];
    const particleCount = 2000;

    for (let i = 0; i < particleCount; i++) {
        vertices.push(
            Math.random() * 40 - 20,
            Math.random() * 40 - 20,
            Math.random() * 40 - 20
        );
        sizes.push(Math.random() * 0.05 + 0.01);
    }

    geometry.setAttribute('position', new THREE.Float32BufferAttribute(vertices, 3));

    // Create custom shader material for "bokeh" effect
    const material = new THREE.PointsMaterial({
        color: 0x0077ff,
        size: 0.03,
        transparent: true,
        opacity: 0.6,
        blending: THREE.AdditiveBlending,
        depthWrite: false,
        sizeAttenuation: true
    });

    particles = new THREE.Points(geometry, material);
    scene.add(particles);

    document.addEventListener('mousemove', onDocumentMouseMove, false);
    window.addEventListener('resize', onWindowResize, false);
    animate();
}

function onDocumentMouseMove(event) {
    targetX = (event.clientX - window.innerWidth / 2) * 0.001;
    targetY = (event.clientY - window.innerHeight / 2) * 0.001;
}

function onWindowResize() {
    camera.aspect = window.innerWidth / window.innerHeight;
    camera.updateProjectionMatrix();
    renderer.setSize(window.innerWidth, window.innerHeight);
}

function animate() {
    requestAnimationFrame(animate);

    if (particles) {
        // Smooth mouse parallax
        mouseX += (targetX - mouseX) * 0.05;
        mouseY += (targetY - mouseY) * 0.05;

        particles.rotation.y += 0.0003 + mouseX * 0.1;
        particles.rotation.x += 0.0001 + mouseY * 0.1;

        // Pulse effect
        const time = Date.now() * 0.0005;
        particles.material.opacity = 0.4 + Math.sin(time) * 0.2;
    }

    renderer.render(scene, camera);
}

if (document.getElementById('canvas-container')) {
    init();
}
