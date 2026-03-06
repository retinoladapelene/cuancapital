/**
 * CuanCapital — Learning Hub Engine (Phase 6)
 * Renders Mini Course UI: course cards, lesson reader, XP toast on completion.
 * Integrates with: GET /api/courses, GET /api/courses/{id}, POST /api/lesson/complete
 */

class LearningHub {

    constructor(containerId) {
        this.container = document.getElementById(containerId);
        this.currentView = 'list'; // 'list' | 'course' | 'lesson'
        this.activeCourse = null;
        this.activeLesson = null;
        if (this.container) this._init();
    }

    async _init() {
        this._renderSkeleton();
        await this.loadCourseList();
    }

    // ─── API Helpers ─────────────────────────────────────────────────────────────

    _headers() {
        const token = localStorage.getItem('auth_token');
        const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
        return {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'Authorization': token ? `Bearer ${token}` : '',
            'X-CSRF-TOKEN': csrf || '',
        };
    }

    async _get(url) {
        const res = await fetch(url, { headers: this._headers() });
        if (!res.ok) throw new Error(`GET ${url} → ${res.status}`);
        return res.json();
    }

    async _post(url, body) {
        const res = await fetch(url, { method: 'POST', headers: this._headers(), body: JSON.stringify(body) });
        if (!res.ok) throw new Error(`POST ${url} → ${res.status}`);
        return res.json();
    }

    // ─── Course List View ─────────────────────────────────────────────────────────

    async loadCourseList() {
        try {
            const json = await this._get('/api/courses');
            this._renderCourseList(json.data || []);
        } catch (err) {
            this._renderError('Gagal memuat kursus. Pastikan kamu sudah login.');
            console.warn('[LearningHub]', err);
        }
    }

    _renderCourseList(courses) {
        if (!this.container) return;

        const levelBadge = (level) => ({
            beginner: 'bg-emerald-500/20 text-emerald-400 border-emerald-500/30',
            intermediate: 'bg-blue-500/20 text-blue-400 border-blue-500/30',
            advanced: 'bg-rose-500/20 text-rose-400 border-rose-500/30',
        }[level] || 'bg-slate-600/20 text-slate-400 border-slate-600/30');

        const courseCards = courses.length === 0
            ? `<div class="text-center py-10 text-slate-500"><i class="fas fa-book-open text-3xl mb-3 opacity-30"></i><p class="text-sm">Belum ada kursus tersedia.</p></div>`
            : courses.map(c => {
                const total = c.lessons_count || 0;
                const done = c.user_completed_lessons || 0;
                const pct = total > 0 ? Math.round((done / total) * 100) : 0;
                const isCompleted = c.user_completed;
                const isLocked = c.is_locked;

                return `
                    <div class="course-card group p-4 rounded-2xl bg-slate-800/70 border 
                                ${isLocked ? 'border-slate-800 opacity-50 cursor-not-allowed' : 'border-slate-700/60 hover:border-slate-500 hover:bg-slate-800 cursor-pointer'} 
                                transition-all duration-300"
                         data-course-id="${c.id}" data-locked="${isLocked ? 'true' : 'false'}">
                        <div class="flex items-start justify-between gap-3 mb-3">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 mb-1.5 flex-wrap">
                                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full border ${levelBadge(c.level)} capitalize">${c.level}</span>
                                    ${isCompleted ? '<span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-amber-500/20 text-amber-400 border border-amber-500/30">✓ Completed</span>' : ''}
                                    ${isLocked ? '<span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-slate-800 text-slate-500 border border-slate-700"><i class="fas fa-lock mr-1"></i>Terkunci</span>' : ''}
                                </div>
                                <h4 class="font-bold text-sm text-white ${!isLocked ? 'group-hover:text-emerald-300' : ''} transition-colors">${c.title}</h4>
                                <p class="text-[10px] text-slate-400 mt-1 line-clamp-2">${c.description || ''}</p>
                            </div>
                            <div class="shrink-0 text-right">
                                <div class="text-lg font-black ${isCompleted ? 'text-amber-400' : 'text-slate-400'}">${c.xp_reward}</div>
                                <div class="text-[9px] text-slate-500">XP</div>
                            </div>
                        </div>

                        <!-- Progress -->
                        <div class="flex items-center gap-2 mt-3">
                            <div class="flex-1 h-1.5 bg-slate-900 rounded-full overflow-hidden border border-white/5">
                                <div class="h-full rounded-full transition-all duration-700 ${isCompleted ? 'bg-amber-400' : 'bg-emerald-500'}"
                                     style="width:${pct}%"></div>
                            </div>
                            <span class="text-[10px] text-slate-500 shrink-0">${done}/${total} lessons</span>
                        </div>
                    </div>
                `;
            }).join('');

        this.container.innerHTML = `
            <div class="mb-4">
                <p class="text-[10px] font-bold text-white uppercase tracking-widest mb-0.5">📚 Learning Hub</p>
                <p class="text-[10px] text-slate-500">${courses.length} kursus tersedia</p>
            </div>
            <div class="space-y-3">${courseCards}</div>
        `;

        // Bind course card clicks
        this.container.querySelectorAll('.course-card').forEach(card => {
            card.addEventListener('click', () => {
                if (card.dataset.locked === 'true') return;
                this.openCourse(parseInt(card.dataset.courseId));
            });
        });
    }

    // ─── Course Detail View ───────────────────────────────────────────────────────

    async openCourse(courseId) {
        this._renderSkeleton();
        try {
            const json = await this._get(`/api/courses/${courseId}`);
            this.activeCourse = json.data;
            this._renderCourseDetail(json.data);
        } catch (err) {
            this._renderError('Gagal memuat kursus.');
        }
    }

    _renderCourseDetail(course) {
        const totalMinutes = course.lessons.reduce((acc, l) => acc + (l.estimated_minutes || 0), 0);

        const lessonItems = course.lessons.map((lesson, i) => {
            const done = lesson.is_completed;
            const isLocked = lesson.is_locked;

            return `
                <div class="lesson-item flex items-center gap-3 p-3 rounded-xl
                            ${done ? 'bg-emerald-500/8 border border-emerald-500/20 cursor-pointer hover:bg-emerald-500/15' :
                    (isLocked ? 'bg-slate-900 border border-slate-800 opacity-60 cursor-not-allowed' : 'bg-slate-800/80 border border-slate-700/60 cursor-pointer hover:border-slate-600')}
                            transition-all duration-200"
                     data-lesson-idx="${i}" data-locked="${isLocked ? 'true' : 'false'}">
                    <div class="w-8 h-8 rounded-full shrink-0 flex items-center justify-center text-sm font-black
                                ${done ? 'bg-emerald-500 text-white shadow-[0_0_10px_rgba(16,185,129,0.4)]' :
                    (isLocked ? 'bg-slate-800 text-slate-600' : 'bg-slate-700 text-slate-400')}">
                        ${done ? '✓' : (isLocked ? '<i class="fas fa-lock text-[10px]"></i>' : i + 1)}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-semibold ${done ? 'text-emerald-300' : 'text-slate-200'} truncate">${lesson.title}</p>
                        <p class="text-[10px] text-slate-500">${lesson.estimated_minutes ? `${lesson.estimated_minutes} min` : 'Baca'} · +${lesson.xp_reward} XP</p>
                    </div>
                    <i class="fas fa-chevron-right text-slate-600 text-xs shrink-0"></i>
                </div>
            `;
        }).join('');

        this.container.innerHTML = `
            <!-- Back button -->
            <button id="lh-back-btn" class="flex items-center gap-2 text-slate-400 hover:text-white text-xs mb-4 transition-colors">
                <i class="fas fa-arrow-left"></i> Kembali
            </button>

            <!-- Course header -->
            <div class="p-4 rounded-2xl bg-gradient-to-br from-emerald-500/10 to-slate-800 border border-emerald-500/20 mb-4">
                <h3 class="font-bold text-white mb-1">${course.title}</h3>
                <p class="text-[10px] text-slate-400 mb-3">${course.description || ''}</p>
                <div class="flex items-center gap-4 text-[10px] text-slate-400">
                    <span><i class="fas fa-book mr-1"></i>${course.lessons_count} lessons</span>
                    ${totalMinutes > 0 ? `<span><i class="fas fa-clock mr-1"></i>${totalMinutes} min</span>` : ''}
                    <span><i class="fas fa-star mr-1 text-amber-400"></i>${course.xp_reward} XP total</span>
                </div>
                <!-- Progress bar -->
                <div class="mt-3">
                    <div class="flex justify-between text-[9px] text-slate-500 mb-1">
                        <span>${course.user_completed_lessons} / ${course.lessons_count} selesai</span>
                        <span>${course.lessons_count > 0 ? Math.round((course.user_completed_lessons / course.lessons_count) * 100) : 0}%</span>
                    </div>
                    <div class="h-1.5 bg-slate-900 rounded-full overflow-hidden">
                        <div class="h-full bg-emerald-500 rounded-full transition-all duration-700"
                             style="width:${course.lessons_count > 0 ? Math.round((course.user_completed_lessons / course.lessons_count) * 100) : 0}%"></div>
                    </div>
                </div>
            </div>

            <!-- Lessons list -->
            <div class="space-y-2" id="lh-lessons-list">${lessonItems}</div>
        `;

        document.getElementById('lh-back-btn')?.addEventListener('click', () => this.loadCourseList());

        this.container.querySelectorAll('.lesson-item').forEach(item => {
            item.addEventListener('click', () => {
                if (item.dataset.locked === 'true') {
                    if (window.gamificationEngine) {
                        window.gamificationEngine.showToast('Materi Terkunci', 'Selesaikan materi sebelumnya terlebih dahulu', 'error');
                    } else {
                        alert('Materi ini masih terkunci.');
                    }
                    return;
                }
                const idx = parseInt(item.dataset.lessonIdx);
                this.openLesson(course.lessons[idx], course);
            });
        });
    }

    // ─── Lesson Reader View ───────────────────────────────────────────────────────

    openLesson(lesson, course) {
        this.activeLesson = lesson;

        // Convert simple markdown to HTML (headings, bold, code, lists, tables, blockquotes)
        const html = this._md(lesson.content);

        this.container.innerHTML = `
            <button id="lh-back-lesson-btn" class="flex items-center gap-2 text-slate-400 hover:text-white text-xs mb-4 transition-colors">
                <i class="fas fa-arrow-left"></i> Kembali ke Kursus
            </button>

            <!-- Lesson header -->
            <div class="mb-4">
                <p class="text-[9px] text-slate-500 uppercase tracking-widest mb-1">${course.title}</p>
                <h3 class="font-bold text-white text-sm mb-1">${lesson.title}</h3>
                <div class="flex items-center gap-3 text-[10px] text-slate-500">
                    ${lesson.estimated_minutes ? `<span><i class="fas fa-clock mr-1"></i>${lesson.estimated_minutes} min</span>` : ''}
                    <span class="text-amber-400 font-bold">+${lesson.xp_reward} XP</span>
                    ${lesson.is_completed ? '<span class="text-emerald-400">✓ Sudah selesai</span>' : ''}
                </div>
            </div>

            <!-- Content -->
            <div class="prose prose-invert prose-sm max-w-none text-slate-300 text-[13px] leading-relaxed mb-6 space-y-3">
                ${html}
            </div>

            <!-- Complete button -->
            <button id="lh-complete-btn"
                    class="w-full py-3.5 rounded-2xl font-bold text-sm transition-all duration-300
                           ${lesson.is_completed
                ? 'bg-slate-700 text-slate-400 cursor-default'
                : 'bg-gradient-to-r from-emerald-500 to-cyan-500 text-white hover:from-emerald-400 hover:to-cyan-400 shadow-[0_4px_20px_rgba(16,185,129,0.35)] hover:scale-105'}"
                    ${lesson.is_completed ? 'disabled' : ''}>
                ${lesson.is_completed ? '✓ Sudah Diselesaikan' : `Selesaikan & Dapat +${lesson.xp_reward} XP 🚀`}
            </button>
        `;

        document.getElementById('lh-back-lesson-btn')?.addEventListener('click', () => this.openCourse(course.id));

        const completeBtn = document.getElementById('lh-complete-btn');
        if (completeBtn && !lesson.is_completed) {
            completeBtn.addEventListener('click', () => this.completeLesson(lesson, course));
        }
    }

    async completeLesson(lesson, course) {
        const btn = document.getElementById('lh-complete-btn');
        if (btn) { btn.disabled = true; btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Menyimpan...'; }

        try {
            const json = await this._post('/api/lesson/complete', { lesson_id: lesson.id });

            if (json.success && json.xp > 0) {
                // Trigger XP popup via gamification engine
                if (window.gamificationEngine) {
                    window.gamificationEngine.showXPAnimation(json.xp, lesson.title);
                    // Refresh XP from server to update the bar
                    await window.gamificationEngine.fetchProgress();
                    window.gamificationEngine.renderUI();
                }
            }

            if (btn) {
                btn.innerHTML = '✓ Sudah Diselesaikan';
                btn.className = 'w-full py-3.5 rounded-2xl font-bold text-sm bg-slate-700 text-slate-400 cursor-default';
            }

            // Reload course detail to reflect updated progress
            setTimeout(() => this.openCourse(course.id), 1200);
        } catch (err) {
            if (btn) { btn.disabled = false; btn.innerHTML = `Selesaikan & Dapat +${lesson.xp_reward} XP 🚀`; }
            console.error('[LearningHub] completeLesson error:', err);
        }
    }

    // ─── Minimal Markdown Renderer ───────────────────────────────────────────────

    _md(text) {
        return text
            .replace(/^### (.+)$/gm, '<h3 class="text-sm font-bold text-white mt-4 mb-1">$1</h3>')
            .replace(/^## (.+)$/gm, '<h2 class="text-base font-bold text-white mt-5 mb-2">$1</h2>')
            .replace(/^# (.+)$/gm, '<h1 class="text-lg font-bold text-white mt-5 mb-2">$1</h1>')
            .replace(/\*\*(.+?)\*\*/g, '<strong class="text-white">$1</strong>')
            .replace(/`([^`]+)`/g, '<code class="bg-slate-700 text-emerald-300 px-1 py-0.5 rounded text-[11px]">$1</code>')
            .replace(/```[\w]*\n([\s\S]+?)```/g, '<pre class="bg-slate-900 border border-slate-700 rounded-xl p-3 text-[11px] text-emerald-300 overflow-x-auto my-3"><code>$1</code></pre>')
            .replace(/^> (.+)$/gm, '<blockquote class="border-l-2 border-emerald-500 pl-3 text-slate-400 italic">$1</blockquote>')
            .replace(/^\| (.+) \|$/gm, (line) => {
                if (line.includes('---')) return '';
                const cells = line.split('|').filter(s => s.trim());
                return '<tr>' + cells.map(c => `<td class="px-3 py-1.5 border border-slate-700 text-[11px]">${c.trim()}</td>`).join('') + '</tr>';
            })
            .replace(/(<tr>[\s\S]+?<\/tr>)/g, '<table class="w-full text-left border-collapse my-3 rounded-xl overflow-hidden">$1</table>')
            .replace(/^[-*] \[ \] (.+)$/gm, '<li class="flex items-start gap-2 text-slate-400"><span class="mt-0.5 w-4 h-4 rounded border border-slate-600 shrink-0"></span>$1</li>')
            .replace(/^- (.+)$/gm, '<li class="flex items-start gap-1.5 text-slate-300"><span class="text-emerald-500 mt-1 shrink-0">•</span>$1</li>')
            .replace(/^\d+\. (.+)$/gm, '<li class="text-slate-300 ml-4 list-decimal">$1</li>')
            .replace(/\n\n/g, '</p><p class="my-2">');
    }

    // ─── State Renderers ──────────────────────────────────────────────────────────

    _renderSkeleton() {
        if (!this.container) return;
        this.container.innerHTML = Array(3).fill(`
            <div class="p-4 rounded-2xl bg-slate-800/50 border border-slate-700/50 animate-pulse mb-3">
                <div class="flex gap-3">
                    <div class="flex-1 space-y-2">
                        <div class="h-3 bg-slate-700 rounded w-1/3"></div>
                        <div class="h-4 bg-slate-700 rounded w-2/3"></div>
                        <div class="h-2 bg-slate-700 rounded w-full mt-2"></div>
                    </div>
                    <div class="w-10 h-10 bg-slate-700 rounded"></div>
                </div>
                <div class="h-1.5 bg-slate-700 rounded-full mt-4"></div>
            </div>
        `).join('');
    }

    _renderError(msg) {
        if (!this.container) return;
        this.container.innerHTML = `
            <div class="text-center py-8 text-slate-500">
                <i class="fas fa-exclamation-triangle text-2xl mb-3 opacity-40"></i>
                <p class="text-sm">${msg}</p>
                <button onclick="window.learningHub.loadCourseList()"
                        class="mt-3 text-xs text-emerald-400 hover:underline">Coba lagi</button>
            </div>
        `;
    }
}

// Auto-init
document.addEventListener('DOMContentLoaded', () => {
    if (document.getElementById('learning-hub-container')) {
        window.learningHub = new LearningHub('learning-hub-container');
    }
});
