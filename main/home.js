// Initialize Swiper
const heroSlider = new Swiper('.hero-slider', {
    slidesPerView: 1,
    spaceBetween: 0,
    loop: true,
    autoplay: {
        delay: 5000,
        disableOnInteraction: false,
    },
    pagination: {
        el: '.swiper-pagination',
        clickable: true,
    },
    navigation: {
        nextEl: '.swiper-button-next',
        prevEl: '.swiper-button-prev',
    },
});

// Sample data for different categories
const contentData = {
    trending: [
        {
            id: 1,
            title: 'การเขียนโค้ดเบื้องต้น',
            excerpt: 'เรียนรู้การเขียนโค้ดสำหรับผู้เริ่มต้น ด้วยภาษา Python',
            image: 'https://via.placeholder.com/400x250',
            author: 'John Doe',
            date: '2024-03-15',
            views: 1500,
            likes: 245
        },
        // Add more trending items
    ],
    knowledge: [
        {
            id: 2,
            title: 'เทคนิคการเรียนรู้ออนไลน์',
            excerpt: 'วิธีการเรียนรู้ออนไลน์ให้มีประสิทธิภาพ',
            image: 'https://via.placeholder.com/400x250',
            author: 'Jane Smith',
            date: '2024-03-14',
            views: 1200,
            likes: 180
        },
        // Add more knowledge items
    ],
    tech: [
        {
            id: 3,
            title: 'AI ในชีวิตประจำวัน',
            excerpt: 'การประยุกต์ใช้ AI ในชีวิตประจำวัน',
            image: 'https://via.placeholder.com/400x250',
            author: 'Tech Expert',
            date: '2024-03-13',
            views: 2000,
            likes: 300
        },
        // Add more tech items
    ],
    news: [
        {
            id: 4,
            title: 'เทรนด์เทคโนโลยี 2024',
            excerpt: 'อัพเดทเทรนด์เทคโนโลยีล่าสุดประจำปี 2024',
            image: 'https://via.placeholder.com/400x250',
            author: 'News Reporter',
            date: '2024-03-12',
            views: 1800,
            likes: 270
        },
        // Add more news items
    ]
};

// Function to create content card
function createContentCard(item) {
    return `
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="content-card">
                <div class="content-card-image">
                    <img src="${item.image}" alt="${item.title}" class="img-fluid">
                </div>
                <div class="content-card-body">
                    <h5 class="content-card-title">${item.title}</h5>
                    <p class="content-card-text">${item.excerpt}</p>
                    <div class="content-card-meta">
                        <span><i class="fas fa-user"></i> ${item.author}</span>
                        <span><i class="fas fa-calendar"></i> ${formatDate(item.date)}</span>
                    </div>
                    <div class="content-card-stats">
                        <span><i class="fas fa-eye"></i> ${item.views}</span>
                        <span><i class="fas fa-heart"></i> ${item.likes}</span>
                    </div>
                </div>
            </div>
        </div>
    `;
}

// Function to format date
function formatDate(dateStr) {
    return new Date(dateStr).toLocaleDateString('th-TH', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
}

// Function to load content for each tab
function loadTabContent(category) {
    const content = contentData[category];
    const container = document.getElementById(`${category}Content`);
    container.innerHTML = content.map(item => createContentCard(item)).join('');
}

// Initialize content when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    // Load initial content for all tabs
    ['trending', 'knowledge', 'tech', 'news'].forEach(category => {
        loadTabContent(category);
    });

    // Add tab change event listeners
    const tabs = document.querySelectorAll('[data-bs-toggle="tab"]');
    tabs.forEach(tab => {
        tab.addEventListener('shown.bs.tab', (event) => {
            const target = event.target.getAttribute('data-bs-target').replace('#', '');
            loadTabContent(target);
        });
    });
});