
<script setup>
import { ref } from "vue";
import { Link } from "@inertiajs/vue3";
import { EyeIcon, PencilSquareIcon, TrashIcon } from "@heroicons/vue/24/solid";
import Swal from "sweetalert2";
import "sweetalert2/dist/sweetalert2.min.css";
import AppLayout from "@/Layouts/AppLayout.vue";

const props = defineProps({
    centers: {
        type: Array,
        required: true,
    },
});

function confirmDelete(center) {
    Swal.fire({
        title: "Delete Center?",
        text: `Are you sure you want to delete "${center.name}"?`,
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#ef4444",
        cancelButtonColor: "#6b7280",
        confirmButtonText: "Yes, delete it!",
        background: document.documentElement.classList.contains('dark') ? "#1f2937" : "#fff",
        color: document.documentElement.classList.contains('dark') ? "#f3f4f6" : "#111827",
    }).then((result) => {
        if (result.isConfirmed) {
            // Replace with your delete logic
            Swal.fire("Deleted!", "The center has been deleted.", "success");
        }
    });
}
</script>

<template>
    
    <AppLayout>
        <div class="distance-home-page">
            <section class="intro-section">
                <h1 class="title">
                    <span class="emoji">📚</span>
                    <span>
                        Distance Learning <span class="highlight">Programs</span>
                    </span>
                </h1>
                <p class="subtitle">
                    Welcome to the <span class="highlight">Distance Education Portal</span>.<br>
                    <span class="highlight">Explore programs</span> by language and level.<br>
                    <span style="color:var(--highlight);font-weight:600;">Manage your academic journey remotely with ease.</span>
                </p>
            </section>

            <section class="centers-section">
                <div class="centers-header">
                    <h2 class="centers-title">
                        <span class="emoji">🏢</span>
                        <span>Available <span class="highlight">Centers</span></span>
                    </h2>
                    <span class="centers-count">
                        <span class="count-badge">{{ centers.length }}</span> centers found
                    </span>
                </div>
                <div class="students-count" style="text-align:right; margin-bottom:1rem;">
                    <span class="count-badge" style="font-size:1.1em;">
                        {{ centers.reduce((sum, c) => sum + (c.students_count || 0), 0) }}
                    </span>
                    <span style="color:var(--subtitle);margin-left:0.4em;">students enrolled</span>
                </div>
                <div class="centers-list">
                    <div v-for="center in centers" :key="center.id" class="center-card">
                        <div class="center-info">
                            <div class="center-name">{{ center.name }}</div>
                            <div class="center-location">
                                <span class="emoji">📍</span>
                                {{ center.location || 'Location not specified' }}
                            </div>
                            <div style="margin-top:0.5em;">
                                <span class="count-badge" style="background:var(--highlight);font-size:0.95em;">
                                    {{ center.students_count || 0 }} students
                                </span>
                            </div>
                        </div>
                        <div class="center-actions">
                            <Link :href="`/centers/${center.id}`" class="action-btn view" title="View">
                                <EyeIcon class="icon" />
                            </Link>
                            <Link :href="`/centers/${center.id}/edit`" class="action-btn edit" title="Edit">
                                <PencilSquareIcon class="icon" />
                            </Link>
                            <button class="action-btn delete" title="Delete" @click="confirmDelete(center)">
                                <TrashIcon class="icon" />
                            </button>
                        </div>
                    </div>
                </div>
                <div v-if="centers.length === 0" class="no-centers">
                    <span class="emoji">😕</span> No centers available at the moment.
                </div>
            </section>
        </div>
    </AppLayout>
</template>


<style scoped>
.distance-home-page {
    max-width: 900px;
    margin: auto;
    padding: 2.5rem 1.5rem;
    background: var(--bg);
    border-radius: 18px;
    box-shadow: 0 4px 32px 0 rgba(31,41,55,0.08);
    transition: background 0.3s;
}
:root {
    --bg: #f9fafb;
    --card-bg: #fff;
    --text: #1f2937;
    --subtitle: #4b5563;
    --highlight: #6366f1;
    --border: #e5e7eb;
    --badge-bg: #6366f1;
    --badge-text: #fff;
    --action-bg: #f3f4f6;
    --action-hover: #e0e7ff;
    --delete: #ef4444;
}
.dark .distance-home-page {
    --bg: #18181b;
    --card-bg: #23232a;
    --text: #f3f4f6;
    --subtitle: #a1a1aa;
    --highlight: #818cf8;
    --border: #27272a;
    --badge-bg: #818cf8;
    --badge-text: #18181b;
    --action-bg: #27272a;
    --action-hover: #3730a3;
    --delete: #f87171;
}

.intro-section {
    text-align: center;
    margin-bottom: 2.5rem;
}
.title {
    font-size: 2.3rem;
    font-weight: 800;
    color: var(--text);
    margin-bottom: 0.5rem;
    letter-spacing: -1px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5em;
}
.emoji {
    font-size: 2rem;
}
.subtitle {
    font-size: 1.15rem;
    color: var(--subtitle);
    margin-top: 0.5rem;
    line-height: 1.6;
}
.highlight {
    color: var(--highlight);
    font-weight: 600;
}

.centers-section {
    background: var(--card-bg);
    border-radius: 12px;
    padding: 1.5rem 1rem;
    box-shadow: 0 2px 12px 0 rgba(31,41,55,0.06);
    margin-top: 1.5rem;
    transition: background 0.3s;
}
.centers-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 1.2rem;
}
.centers-title {
    font-size: 1.3rem;
    font-weight: 700;
    color: var(--text);
    display: flex;
    align-items: center;
    gap: 0.5em;
}
.centers-count {
    font-size: 1rem;
    color: var(--subtitle);
    display: flex;
    align-items: center;
    gap: 0.3em;
}
.count-badge {
    background: var(--badge-bg);
    color: var(--badge-text);
    border-radius: 999px;
    padding: 0.2em 0.8em;
    font-weight: 700;
    font-size: 1.05em;
    margin-right: 0.3em;
    box-shadow: 0 1px 4px 0 rgba(99,102,241,0.08);
}

.centers-list {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
    gap: 1.2rem;
}
.center-card {
    background: var(--card-bg);
    border: 1px solid var(--border);
    border-radius: 10px;
    padding: 1.1rem 1rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
    transition: box-shadow 0.2s, border 0.2s;
    box-shadow: 0 1px 6px 0 rgba(31,41,55,0.04);
}
.center-card:hover {
    border-color: var(--highlight);
    box-shadow: 0 4px 16px 0 rgba(99,102,241,0.08);
}
.center-info {
    flex: 1;
}
.center-name {
    font-size: 1.1rem;
    font-weight: 600;
    color: var(--text);
    margin-bottom: 0.2em;
}
.center-location {
    font-size: 0.98rem;
    color: var(--subtitle);
}
.center-actions {
    display: flex;
    gap: 0.4em;
    margin-left: 1em;
}
.action-btn {
    background: var(--action-bg);
    border: none;
    border-radius: 6px;
    padding: 0.35em 0.5em;
    cursor: pointer;
    transition: background 0.18s;
    display: flex;
    align-items: center;
    justify-content: center;
}
.action-btn.view:hover {
    background: var(--action-hover);
}
.action-btn.edit:hover {
    background: var(--highlight);
    color: #fff;
}
.action-btn.delete:hover {
    background: var(--delete);
    color: #fff;
}
.icon {
    width: 1.2em;
    height: 1.2em;
}
.no-centers {
    text-align: center;
    color: var(--subtitle);
    padding: 2em 0;
    font-size: 1.1rem;
}
</style>

<style scoped>
.distance-home-page {
  max-width: 960px;
  margin: auto;
  padding: 2rem;
  background: #f9fafb;
  border-radius: 12px;
}

.intro-section {
  text-align: center;
  margin-bottom: 2rem;
}

.title {
  font-size: 2rem;
  font-weight: 700;
  color: #1f2937;
}

.subtitle {
  font-size: 1.1rem;
  color: #4b5563;
}
</style>


