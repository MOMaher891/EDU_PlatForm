class LearningInterface {
    constructor() {
        this.currentLesson = null;
        this.videoPlayer = null;
        this.progressInterval = null;
        this.watchTime = 0;
        this.lastProgressUpdate = 0;
        this.isCompleted = false;

        this.init();
    }

    init() {
        this.setupEventListeners();
        this.initializeVideoPlayer();
        this.loadLessonProgress();
        this.setupNavigation();
        this.setupSidebarTabs();
        this.setupFullscreen();
        this.setupSpeedControls();
    }

    setupEventListeners() {
        // Lesson navigation
        document.getElementById('prevLesson')?.addEventListener('click', () => this.navigateToLesson('prev'));
        document.getElementById('nextLesson')?.addEventListener('click', () => this.navigateToLesson('next'));

        // Video progress tracking
        const video = document.getElementById('lessonVideo');
        if (video) {
            console.log('Setting up video event listeners');
            video.addEventListener('timeupdate', () => this.trackVideoProgress());
            video.addEventListener('ended', () => this.onVideoEnded());
            video.addEventListener('play', () => this.onVideoPlay());
            video.addEventListener('pause', () => this.onVideoPause());
            video.addEventListener('error', (e) => {
                console.error('Video error:', e);
                console.error('Video error details:', video.error);
            });
            video.addEventListener('loadeddata', () => {
                console.log('Video data loaded successfully');
            });
        } else {
            console.log('No video element found for event listeners');
        }

        // Progress update buttons
        document.getElementById('markComplete')?.addEventListener('click', () => this.markLessonComplete());
        document.getElementById('markIncomplete')?.addEventListener('click', () => this.markLessonIncomplete());

        // File download
        document.querySelectorAll('.download-btn').forEach(btn => {
            btn.addEventListener('click', (e) => this.handleFileDownload(e));
        });

        // Notes functionality
        document.getElementById('saveNote')?.addEventListener('click', () => this.saveNote());
        document.getElementById('clearNote')?.addEventListener('click', () => this.clearNote());

        // Bookmark functionality
        document.querySelectorAll('.bookmark-btn').forEach(btn => {
            btn.addEventListener('click', () => this.toggleBookmark());
        });

        // Keyboard shortcuts
        document.addEventListener('keydown', (e) => this.handleKeyboardShortcuts(e));
    }

    initializeVideoPlayer() {
        const video = document.getElementById('lessonVideo');
        if (!video) {
            console.log('No video element found with id "lessonVideo"');
            // Check if there are any video elements on the page
            const allVideos = document.querySelectorAll('video');
            console.log('Total video elements on page:', allVideos.length);
            allVideos.forEach((v, index) => {
                console.log(`Video ${index}:`, v);
                console.log(`Video ${index} src:`, v.src);
                console.log(`Video ${index} sources:`, v.querySelectorAll('source'));
            });
            return;
        }

        console.log('Video element found:', video);
        console.log('Video src:', video.src);
        console.log('Video sources:', video.querySelectorAll('source'));

        // Log source details
        const sources = video.querySelectorAll('source');
        sources.forEach((source, index) => {
            console.log(`Source ${index}:`, source.src, 'Type:', source.type);
        });

        this.videoPlayer = video;

        // Set up video controls
        this.setupVideoControls();

        // Load saved progress
        this.loadVideoProgress();
    }

    setupVideoControls() {
        // Custom video controls
        const videoContainer = document.querySelector('.video-container');
        if (!videoContainer) {
            console.log('No video container found');
            return;
        }

        console.log('Video container found:', videoContainer);

        // Create custom controls
        const controls = document.createElement('div');
        controls.className = 'custom-video-controls';
        controls.innerHTML = `
            <div class="progress-bar">
                <div class="progress-fill"></div>
            </div>
            <div class="controls-row">
                <button class="play-pause-btn">
                    <i class="fas fa-play"></i>
                </button>
                <div class="time-display">
                    <span class="current-time">0:00</span>
                    <span>/</span>
                    <span class="total-time">0:00</span>
                </div>
                <div class="volume-control">
                    <button class="mute-btn">
                        <i class="fas fa-volume-up"></i>
                    </button>
                    <input type="range" class="volume-slider" min="0" max="1" step="0.1" value="1">
                </div>
                <button class="fullscreen-btn">
                    <i class="fas fa-expand"></i>
                </button>
            </div>
        `;

        videoContainer.appendChild(controls);
        this.setupCustomControls(controls);
    }

    setupCustomControls(controls) {
        const video = this.videoPlayer;
        const playPauseBtn = controls.querySelector('.play-pause-btn');
        const progressBar = controls.querySelector('.progress-bar');
        const progressFill = controls.querySelector('.progress-fill');
        const currentTimeSpan = controls.querySelector('.current-time');
        const totalTimeSpan = controls.querySelector('.total-time');
        const muteBtn = controls.querySelector('.mute-btn');
        const volumeSlider = controls.querySelector('.volume-slider');
        const fullscreenBtn = controls.querySelector('.fullscreen-btn');

        // Play/Pause
        playPauseBtn.addEventListener('click', () => {
            if (video.paused) {
                video.play();
            } else {
                video.pause();
            }
        });

        // Progress bar
        progressBar.addEventListener('click', (e) => {
            const rect = progressBar.getBoundingClientRect();
            const percent = (e.clientX - rect.left) / rect.width;
            video.currentTime = percent * video.duration;
        });

        // Volume
        muteBtn.addEventListener('click', () => {
            video.muted = !video.muted;
            muteBtn.innerHTML = video.muted ? '<i class="fas fa-volume-mute"></i>' : '<i class="fas fa-volume-up"></i>';
        });

        volumeSlider.addEventListener('input', (e) => {
            video.volume = e.target.value;
            video.muted = e.target.value === 0;
        });

        // Fullscreen
        fullscreenBtn.addEventListener('click', () => {
            if (document.fullscreenElement) {
                document.exitFullscreen();
            } else {
                videoContainer.requestFullscreen();
            }
        });

        // Update controls
        video.addEventListener('timeupdate', () => {
            const percent = (video.currentTime / video.duration) * 100;
            progressFill.style.width = percent + '%';
            currentTimeSpan.textContent = this.formatTime(video.currentTime);
        });

        video.addEventListener('loadedmetadata', () => {
            totalTimeSpan.textContent = this.formatTime(video.duration);
        });

        video.addEventListener('play', () => {
            playPauseBtn.innerHTML = '<i class="fas fa-pause"></i>';
        });

        video.addEventListener('pause', () => {
            playPauseBtn.innerHTML = '<i class="fas fa-play"></i>';
        });
    }

    trackVideoProgress() {
        if (!this.videoPlayer) return;

        const currentTime = Math.floor(this.videoPlayer.currentTime);

        // Update watch time every 5 seconds
        if (currentTime - this.lastProgressUpdate >= 5) {
            this.watchTime = currentTime;
            this.lastProgressUpdate = currentTime;
            this.updateProgress();
        }

        // Auto-mark as complete when 90% watched
        if (!this.isCompleted && this.videoPlayer.duration > 0) {
            const progressPercent = (currentTime / this.videoPlayer.duration) * 100;
            if (progressPercent >= 90) {
                this.markLessonComplete();
            }
        }

        // Update progress bar
        this.updateVideoProgressBar(currentTime);
    }

    updateVideoProgressBar(currentTime) {
        const progressBar = document.querySelector('.video-progress-bar');
        if (progressBar && this.videoPlayer.duration > 0) {
            const progressPercent = (currentTime / this.videoPlayer.duration) * 100;
            progressBar.style.width = progressPercent + '%';
        }
    }

    onVideoEnded() {
        this.markLessonComplete();
        this.showCompletionMessage();
    }

    onVideoPlay() {
        this.startProgressTracking();
    }

    onVideoPause() {
        this.stopProgressTracking();
    }

    startProgressTracking() {
        this.progressInterval = setInterval(() => {
            this.updateProgress();
        }, 30000); // Update every 30 seconds
    }

    stopProgressTracking() {
        if (this.progressInterval) {
            clearInterval(this.progressInterval);
            this.progressInterval = null;
        }
    }

    async updateProgress() {
        if (!this.currentLesson) return;

        try {
            const response = await fetch(`/student/lessons/${this.currentLesson}/progress`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    is_completed: this.isCompleted,
                    watch_time: this.watchTime
                })
            });

            const data = await response.json();

            if (data.success) {
                this.updateProgressUI(data.course_progress);
            }
        } catch (error) {
            console.error('Error updating progress:', error);
        }
    }

    async loadLessonProgress() {
        if (!this.currentLesson) return;

        try {
            const response = await fetch(`/student/lessons/${this.currentLesson}/progress`);
            const data = await response.json();

            if (data.progress) {
                this.isCompleted = data.progress.is_completed;
                this.watchTime = data.progress.watch_time;
                this.updateProgressUI();
                this.loadVideoProgress();
            }
        } catch (error) {
            console.error('Error loading progress:', error);
        }
    }

    loadVideoProgress() {
        if (!this.videoPlayer || this.watchTime === 0) return;

        // Resume from where user left off (but not too close to the end)
        const resumeTime = Math.min(this.watchTime, this.videoPlayer.duration - 10);
        this.videoPlayer.currentTime = resumeTime;
    }

    async markLessonComplete() {
        this.isCompleted = true;

        try {
            const response = await fetch(`/student/lessons/${this.currentLesson}/complete`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    watch_time: this.watchTime
                })
            });

            const data = await response.json();

            if (data.success) {
                this.updateProgressUI(data.course_progress);
                this.updateLessonUI();
                this.showCompletionMessage();

                // Check if course is completed
                if (data.is_course_completed) {
                    this.showCourseCompletionMessage();
                } else if (data.next_lesson) {
                    // Show next lesson prompt
                    this.showNextLessonPrompt(data.next_lesson);
                }
            }
        } catch (error) {
            console.error('Error completing lesson:', error);
            // Fallback to old method
            await this.updateProgress();
            this.updateLessonUI();
            this.showCompletionMessage();
        }
    }

    async markLessonIncomplete() {
        this.isCompleted = false;
        await this.updateProgress();
        this.updateLessonUI();
    }

    updateProgressUI(courseProgress = null) {
        // Update lesson completion status
        const lessonItem = document.querySelector(`[data-lesson-id="${this.currentLesson}"]`);
        if (lessonItem) {
            if (this.isCompleted) {
                lessonItem.classList.add('completed');
                lessonItem.classList.remove('in-progress');
            } else {
                lessonItem.classList.remove('completed');
                lessonItem.classList.add('in-progress');
            }
        }

        // Update course progress
        if (courseProgress !== null) {
            const progressBar = document.querySelector('.progress-fill');
            const progressText = document.querySelector('.progress-percentage');

            if (progressBar) progressBar.style.width = courseProgress + '%';
            if (progressText) progressText.textContent = Math.round(courseProgress) + '%';
        }

        // Update completed lessons count
        const completedCount = document.querySelector('.completed-lessons');
        if (completedCount) {
            const currentCount = parseInt(completedCount.textContent);
            if (this.isCompleted) {
                completedCount.textContent = currentCount + 1;
            } else {
                completedCount.textContent = Math.max(0, currentCount - 1);
            }
        }
    }

    updateLessonUI() {
        const completeBtn = document.getElementById('markComplete');
        const incompleteBtn = document.getElementById('markIncomplete');

        if (completeBtn && incompleteBtn) {
            if (this.isCompleted) {
                completeBtn.style.display = 'none';
                incompleteBtn.style.display = 'block';
            } else {
                completeBtn.style.display = 'block';
                incompleteBtn.style.display = 'none';
            }
        }
    }

    showCompletionMessage() {
        const message = document.createElement('div');
        message.className = 'completion-message';
        message.innerHTML = `
            <div class="completion-content">
                <i class="fas fa-check-circle text-success"></i>
                <h4>تم إكمال الدرس بنجاح!</h4>
                <p>يمكنك الآن الانتقال إلى الدرس التالي</p>
                <button class="btn btn-primary" onclick="this.parentElement.parentElement.remove()">
                    متابعة
                </button>
            </div>
        `;

        document.body.appendChild(message);

        // Auto-remove after 5 seconds
        setTimeout(() => {
            if (message.parentElement) {
                message.remove();
            }
        }, 5000);
    }

    showCourseCompletionMessage() {
        const message = document.createElement('div');
        message.className = 'course-completion-message';
        message.innerHTML = `
            <div class="completion-content">
                <i class="fas fa-trophy text-warning"></i>
                <h4>مبروك! لقد أكملت الكورس!</h4>
                <p>أحسنت! لقد أكملت جميع دروس هذا الكورس بنجاح.</p>
                <div class="completion-actions">
                    <button class="btn btn-primary" onclick="window.location.href='/student/dashboard'">
                        العودة للوحة التحكم
                    </button>
                    <button class="btn btn-secondary" onclick="window.location.href='/student/enrolled-courses'">
                        عرض الكورسات المسجلة
                    </button>
                </div>
            </div>
        `;

        document.body.appendChild(message);
    }

    showNextLessonPrompt(nextLesson) {
        const message = document.createElement('div');
        message.className = 'next-lesson-prompt';
        message.innerHTML = `
            <div class="prompt-content">
                <i class="fas fa-arrow-right text-primary"></i>
                <h4>الدرس التالي جاهز!</h4>
                <p>${nextLesson.title}</p>
                <div class="prompt-actions">
                    <button class="btn btn-primary" onclick="window.location.href='${nextLesson.url}'">
                        الانتقال للدرس التالي
                    </button>
                    <button class="btn btn-secondary" onclick="this.parentElement.parentElement.parentElement.remove()">
                        البقاء في هذا الدرس
                    </button>
                </div>
            </div>
        `;

        document.body.appendChild(message);

        // Auto-remove after 10 seconds
        setTimeout(() => {
            if (message.parentElement) {
                message.remove();
            }
        }, 10000);
    }

    navigateToLesson(direction) {
        const currentUrl = new URL(window.location);
        const lessonParam = direction === 'next' ? this.nextLessonId : this.prevLessonId;

        if (lessonParam) {
            currentUrl.searchParams.set('lesson', lessonParam);
            window.location.href = currentUrl.toString();
        }
    }

    setupNavigation() {
        // Enable/disable navigation buttons
        const prevBtn = document.getElementById('prevLesson');
        const nextBtn = document.getElementById('nextLesson');

        if (prevBtn) {
            prevBtn.disabled = !this.prevLessonId;
        }

        if (nextBtn) {
            nextBtn.disabled = !this.nextLessonId;
        }
    }

    setupSidebarTabs() {
        const tabBtns = document.querySelectorAll('.tab-btn');
        const tabContents = document.querySelectorAll('.tab-content');

        tabBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                const tabName = btn.dataset.tab;

                // Update active tab button
                tabBtns.forEach(b => b.classList.remove('active'));
                btn.classList.add('active');

                // Show corresponding content
                tabContents.forEach(content => {
                    content.style.display = content.dataset.tab === tabName ? 'block' : 'none';
                });
            });
        });
    }

    setupFullscreen() {
        const fullscreenBtn = document.querySelector('.fullscreen-btn');
        if (!fullscreenBtn) return;

        fullscreenBtn.addEventListener('click', () => {
            const contentSection = document.querySelector('.content-section');

            if (document.fullscreenElement) {
                document.exitFullscreen();
            } else {
                contentSection.requestFullscreen();
            }
        });
    }

    setupSpeedControls() {
        const speedBtn = document.querySelector('.speed-btn');
        if (!speedBtn || !this.videoPlayer) return;

        const speeds = [0.5, 0.75, 1, 1.25, 1.5, 2];
        let currentSpeedIndex = 2; // Default 1x

        speedBtn.addEventListener('click', () => {
            currentSpeedIndex = (currentSpeedIndex + 1) % speeds.length;
            const newSpeed = speeds[currentSpeedIndex];

            this.videoPlayer.playbackRate = newSpeed;
            speedBtn.innerHTML = `<i class="fas fa-tachometer-alt"></i> ${newSpeed}x`;
        });
    }

    async handleFileDownload(e) {
        e.preventDefault();

        const lessonId = e.target.dataset.lessonId;
        if (!lessonId) return;

        try {
            window.open(`/student/lessons/${lessonId}/download`, '_blank');
        } catch (error) {
            console.error('Error downloading file:', error);
            this.showError('حدث خطأ أثناء تحميل الملف');
        }
    }

    async saveNote() {
        const noteText = document.getElementById('lessonNote').value;
        const lessonId = this.currentLesson;

        if (!noteText.trim()) return;

        try {
            // Save note to localStorage for now (can be extended to save to database)
            const notes = JSON.parse(localStorage.getItem('lessonNotes') || '{}');
            notes[lessonId] = {
                text: noteText,
                timestamp: new Date().toISOString()
            };
            localStorage.setItem('lessonNotes', JSON.stringify(notes));

            this.showSuccess('تم حفظ الملاحظة بنجاح');
        } catch (error) {
            console.error('Error saving note:', error);
            this.showError('حدث خطأ أثناء حفظ الملاحظة');
        }
    }

    clearNote() {
        document.getElementById('lessonNote').value = '';
        this.showSuccess('تم مسح الملاحظة');
    }

    toggleBookmark() {
        const lessonId = this.currentLesson;
        const bookmarks = JSON.parse(localStorage.getItem('lessonBookmarks') || '[]');
        const bookmarkIndex = bookmarks.indexOf(lessonId);

        if (bookmarkIndex > -1) {
            bookmarks.splice(bookmarkIndex, 1);
            this.showSuccess('تم إزالة الإشارة المرجعية');
        } else {
            bookmarks.push(lessonId);
            this.showSuccess('تم إضافة الإشارة المرجعية');
        }

        localStorage.setItem('lessonBookmarks', JSON.stringify(bookmarks));
        this.updateBookmarkUI();
    }

    updateBookmarkUI() {
        const lessonId = this.currentLesson;
        const bookmarks = JSON.parse(localStorage.getItem('lessonBookmarks') || '[]');
        const isBookmarked = bookmarks.includes(lessonId);

        const bookmarkBtns = document.querySelectorAll('.bookmark-btn');
        bookmarkBtns.forEach(btn => {
            const icon = btn.querySelector('i');
            if (icon) {
                icon.className = isBookmarked ? 'fas fa-bookmark text-primary' : 'fas fa-bookmark';
            }
        });
    }

    handleKeyboardShortcuts(e) {
        // Space bar to play/pause video
        if (e.code === 'Space' && this.videoPlayer) {
            e.preventDefault();
            if (this.videoPlayer.paused) {
                this.videoPlayer.play();
            } else {
                this.videoPlayer.pause();
            }
        }

        // Arrow keys for navigation
        if (e.code === 'ArrowLeft' && this.prevLessonId) {
            e.preventDefault();
            this.navigateToLesson('prev');
        }

        if (e.code === 'ArrowRight' && this.nextLessonId) {
            e.preventDefault();
            this.navigateToLesson('next');
        }

        // M key to mute/unmute
        if (e.code === 'KeyM' && this.videoPlayer) {
            e.preventDefault();
            this.videoPlayer.muted = !this.videoPlayer.muted;
        }

        // F key for fullscreen
        if (e.code === 'KeyF') {
            e.preventDefault();
            const contentSection = document.querySelector('.content-section');
            if (document.fullscreenElement) {
                document.exitFullscreen();
            } else {
                contentSection.requestFullscreen();
            }
        }
    }

    formatTime(seconds) {
        const minutes = Math.floor(seconds / 60);
        const remainingSeconds = Math.floor(seconds % 60);
        return `${minutes}:${remainingSeconds.toString().padStart(2, '0')}`;
    }

    showSuccess(message) {
        this.showNotification(message, 'success');
    }

    showError(message) {
        this.showNotification(message, 'error');
    }

    showNotification(message, type) {
        const notification = document.createElement('div');
        notification.className = `notification notification-${type}`;
        notification.textContent = message;

        document.body.appendChild(notification);

        setTimeout(() => {
            notification.classList.add('show');
        }, 100);

        setTimeout(() => {
            notification.classList.remove('show');
            setTimeout(() => notification.remove(), 300);
        }, 3000);
    }
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    window.learningInterface = new LearningInterface();
});

// Export for global access
window.LearningInterface = LearningInterface;
