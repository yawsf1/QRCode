<script setup>
import { usePage } from "@inertiajs/vue3";
import MainLink from "../components/Links/MainLink.vue";
import { route } from "ziggy-js";
import { computed, ref } from "vue";

const page = usePage();
const user = computed(() => page.props.auth.user);
const openFaq = ref(null);

const dashboardRoute = computed(() => {
    if (!user.value) return route("user.login");
    if (user.value.role === "super_admin")
        return route("super-admin.dashboard");
    if (user.value.role === "admin") return route("admin.dashboard");
    return route("employe.dashboard");
});

const faqItems = [
    {
        q: "Quels sont les prérequis matériels ?",
        a: "Aucun achat nécessaire. L'administrateur RH affiche ou imprime le code QR, et les employés utilisent leur propre smartphone pour scanner.",
    },
    {
        q: "Comment le système empêche-t-il la fraude au pointage ?",
        a: "Les scans reposent sur des tokens d'authentification uniques liés au compte actif de l'utilisateur. Il est impossible de pointer à la place d'un collègue absent.",
    },
    {
        q: "Peut-on exporter les données vers les logiciels de paie ?",
        a: "Oui. Toutes les statistiques (présences, absences, retards, départs anticipés) sont exportables instantanément pour vos gestionnaires de paie.",
    },
    {
        q: "Est-ce adapté aux entreprises avec des horaires d'équipes différents ?",
        a: "Absolument. Vous pouvez configurer des horaires et des jours ouvrés distincts pour chaque employé de manière 100% individualisée.",
    },
    {
        q: "Le déploiement est-il vraiment possible en 5 minutes ?",
        a: "Oui. Créez votre compte, invitez vos employés par email, configurez les horaires — c'est tout. Aucune installation serveur, aucun technicien requis.",
    },
    {
        q: "Les données sont-elles stockées de manière sécurisée ?",
        a: "Toutes les données sont chiffrées en transit et au repos. Notre infrastructure est hébergée sur des serveurs conformes aux normes de sécurité les plus strictes.",
    },
];

const toggleFaq = (i) => {
    openFaq.value = openFaq.value === i ? null : i;
};
</script>

<template>
    <div class="homePage">
        <!-- HERO -->
        <section class="heroSection">
            <div class="heroBg">
                <div class="gridPattern"></div>
                <div class="glowOrb orb1"></div>
                <div class="glowOrb orb2"></div>
            </div>
            <div class="heroInner">
                <div class="heroLeft">
                    <div class="eyebrow">
                        <span class="eyebrowDot"></span>
                        Conforme au Code du Travail Marocain
                    </div>
                    <h1 class="heroTitle">
                        Le pointage<br />
                        <em>réinventé.</em>
                    </h1>
                    <p class="heroSub">
                        Fini les pointeuses à 4 000 DH et les tableurs
                        falsifiés. QRCoded transforme n'importe quel smartphone
                        en terminal de présence sécurisé, en 5 minutes.
                    </p>
                    <div class="heroCtas">
                        <MainLink
                            v-if="!user"
                            :link="route('user.login')"
                            text="Déployer maintenant"
                        />
                        <MainLink
                            v-else
                            :link="dashboardRoute"
                            text="Accéder au Dashboard"
                        />
                        <a href="#workflow" class="ghostBtn">
                            <span>Voir comment ça marche</span>
                            <svg
                                width="16"
                                height="16"
                                viewBox="0 0 16 16"
                                fill="none"
                            >
                                <path
                                    d="M3 8h10M9 4l4 4-4 4"
                                    stroke="currentColor"
                                    stroke-width="1.5"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                />
                            </svg>
                        </a>
                    </div>
                </div>
                <div class="heroRight">
                    <div class="dashMockup">
                        <div class="mockHeader">
                            <div class="mockDots">
                                <span></span><span></span><span></span>
                            </div>
                            <div class="mockUrl">app.qrcoded.ma/dashboard</div>
                            <div class="mockStatus">
                                <span class="pulse"></span>Live
                            </div>
                        </div>
                        <div class="mockBody">
                            <div class="mockSidebar">
                                <div class="mockSideItem active">
                                    <span class="material-symbols-rounded"
                                        >grid_view</span
                                    >
                                </div>
                                <div class="mockSideItem">
                                    <span class="material-symbols-rounded"
                                        >badge</span
                                    >
                                </div>
                                <div class="mockSideItem">
                                    <span class="material-symbols-rounded"
                                        >bar_chart</span
                                    >
                                </div>
                                <div class="mockSideItem">
                                    <span class="material-symbols-rounded"
                                        >calendar_month</span
                                    >
                                </div>
                                <div class="mockSideItem">
                                    <span class="material-symbols-rounded"
                                        >settings</span
                                    >
                                </div>
                            </div>
                            <div class="mockContent">
                                <div class="mockTopRow">
                                    <div class="mockStatCard stat1">
                                        <span class="statVal">94%</span>
                                        <span class="statLabel"
                                            >Présence aujourd'hui</span
                                        >
                                    </div>
                                    <div class="mockStatCard stat2">
                                        <span class="statVal">3</span>
                                        <span class="statLabel"
                                            >Retards signalés</span
                                        >
                                    </div>
                                    <div class="mockStatCard stat3">
                                        <span class="statVal">128</span>
                                        <span class="statLabel"
                                            >Employés actifs</span
                                        >
                                    </div>
                                </div>
                                <div class="mockChart">
                                    <div class="chartLabel">
                                        Présences — 7 derniers jours
                                    </div>
                                    <div class="chartBars">
                                        <div class="bar" style="--h: 75%">
                                            <span>L</span>
                                        </div>
                                        <div class="bar" style="--h: 90%">
                                            <span>M</span>
                                        </div>
                                        <div class="bar" style="--h: 85%">
                                            <span>M</span>
                                        </div>
                                        <div class="bar" style="--h: 95%">
                                            <span>J</span>
                                        </div>
                                        <div
                                            class="bar active"
                                            style="--h: 88%"
                                        >
                                            <span>V</span>
                                        </div>
                                        <div class="bar dim" style="--h: 30%">
                                            <span>S</span>
                                        </div>
                                        <div class="bar dim" style="--h: 10%">
                                            <span>D</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="mockEmployeeList">
                                    <div class="empRow">
                                        <div class="empAvatar a1">SK</div>
                                        <div class="empInfo">
                                            <span class="empName"
                                                >Salma K.</span
                                            >
                                            <span class="empTime"
                                                >Arrivée 08:47</span
                                            >
                                        </div>
                                        <div class="empBadge on-time">
                                            À l'heure
                                        </div>
                                    </div>
                                    <div class="empRow">
                                        <div class="empAvatar a2">MR</div>
                                        <div class="empInfo">
                                            <span class="empName"
                                                >Mohamed R.</span
                                            >
                                            <span class="empTime"
                                                >Arrivée 09:14</span
                                            >
                                        </div>
                                        <div class="empBadge late">+14 min</div>
                                    </div>
                                    <div class="empRow">
                                        <div class="empAvatar a3">FZ</div>
                                        <div class="empInfo">
                                            <span class="empName"
                                                >Fatima Z.</span
                                            >
                                            <span class="empTime"
                                                >Arrivée 08:59</span
                                            >
                                        </div>
                                        <div class="empBadge on-time">
                                            À l'heure
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- METRICS RIBBON -->
        <section class="metricsSection">
            <div class="metricsInner">
                <div class="metricItem">
                    <div class="metricNum">-75%</div>
                    <div class="metricDesc">de charge administrative RH</div>
                </div>
                <div class="metricDivider"></div>
                <div class="metricItem">
                    <div class="metricNum">0 DH</div>
                    <div class="metricDesc">de matériel ou d'installation</div>
                </div>
                <div class="metricDivider"></div>
                <div class="metricItem">
                    <div class="metricNum">5 min</div>
                    <div class="metricDesc">pour un déploiement complet</div>
                </div>
                <div class="metricDivider"></div>
                <div class="metricItem">
                    <div class="metricNum">100%</div>
                    <div class="metricDesc">
                        données chiffrées & infalsifiables
                    </div>
                </div>
            </div>
        </section>

        <!-- FEATURES -->
        <section id="features" class="featuresSection">
            <div class="sectionContainer">
                <div class="sectionLabel">Fonctionnalités</div>
                <h2 class="sectionTitle">
                    Tout ce dont vos RH ont besoin,<br /><em
                        >rien de superflu.</em
                    >
                </h2>
                <div class="featuresGrid">
                    <div class="featureCard large">
                        <div class="featureIcon">
                            <span class="material-symbols-rounded"
                                >qr_code_scanner</span
                            >
                        </div>
                        <div class="featureBody">
                            <h3>Scans QR Sécurisés</h3>
                            <p>
                                Un code QR dynamique, régénéré à chaque session.
                                Vos collaborateurs pointent leur arrivée et
                                départ via leur propre smartphone — sans
                                application tierce à installer.
                            </p>
                        </div>
                        <div class="featureAccent"></div>
                    </div>
                    <div class="featureCard">
                        <div class="featureIcon amber">
                            <span class="material-symbols-rounded"
                                >alarm_smart_wake</span
                            >
                        </div>
                        <h3>Calcul de Retard Intelligent</h3>
                        <p>
                            Définissez une marge de tolérance configurable. Le
                            système calcule automatiquement les minutes exactes
                            de retard par employé.
                        </p>
                    </div>
                    <div class="featureCard">
                        <div class="featureIcon blue">
                            <span class="material-symbols-rounded"
                                >calendar_month</span
                            >
                        </div>
                        <h3>Plannings Sur-mesure</h3>
                        <p>
                            Jours ouvrés et horaires entièrement
                            personnalisables par profil de poste — shifts de
                            nuit, équipes weekend, temps partiel.
                        </p>
                    </div>
                    <div class="featureCard">
                        <div class="featureIcon green">
                            <span class="material-symbols-rounded"
                                >download</span
                            >
                        </div>
                        <h3>Export Paie Instantané</h3>
                        <p>
                            Toutes les données de présences, absences et retards
                            sont exportables pour vos logiciels de paie en un
                            clic.
                        </p>
                    </div>
                    <div class="featureCard">
                        <div class="featureIcon purple">
                            <span class="material-symbols-rounded">lock</span>
                        </div>
                        <h3>Anti-fraude Intégré</h3>
                        <p>
                            Tokens d'authentification uniques liés au compte
                            actif. Un employé ne peut pas pointer à distance
                            pour un collègue absent.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- WORKFLOW / ROLES -->
        <section id="workflow" class="workflowSection">
            <div class="sectionContainer">
                <div class="workflowGrid">
                    <div class="workflowLeft">
                        <div class="sectionLabel">Architecture Système</div>
                        <h2 class="sectionTitle">
                            Conçu pour les structures <em>complexes.</em>
                        </h2>
                        <p class="workflowIntro">
                            Notre hiérarchie de rôles s'adapte nativement à la
                            topologie de votre organisation, quelle que soit sa
                            taille.
                        </p>
                        <div class="rolesStack">
                            <div class="roleItem">
                                <div class="roleIconWrap gold">
                                    <span class="material-symbols-rounded"
                                        >shield_person</span
                                    >
                                </div>
                                <div class="roleContent">
                                    <h4>Super Administrateur</h4>
                                    <p>
                                        Gestion globale de la plateforme,
                                        création de comptes entreprises, audits
                                        complets du système.
                                    </p>
                                </div>
                            </div>
                            <div class="roleLine"></div>
                            <div class="roleItem">
                                <div class="roleIconWrap dark">
                                    <span class="material-symbols-rounded"
                                        >admin_panel_settings</span
                                    >
                                </div>
                                <div class="roleContent">
                                    <h4>Administrateur RH</h4>
                                    <p>
                                        Gestion du registre employés, ajustement
                                        des horaires collectifs, export des
                                        fiches de paie.
                                    </p>
                                </div>
                            </div>
                            <div class="roleLine"></div>
                            <div class="roleItem">
                                <div class="roleIconWrap blue">
                                    <span class="material-symbols-rounded"
                                        >badge</span
                                    >
                                </div>
                                <div class="roleContent">
                                    <h4>Collaborateur</h4>
                                    <p>
                                        Tableau de bord personnel pour suivre
                                        ses statistiques mensuelles de présence
                                        et ses retards.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="workflowRight">
                        <div class="orgChart">
                            <div class="orgNode root">
                                <span class="material-symbols-rounded"
                                    >corporate_fare</span
                                >
                                Direction Générale
                                <div class="nodeTag">Super Admin</div>
                            </div>
                            <div class="orgBranch">
                                <div class="orgBranchLine"></div>
                                <div class="orgChildren">
                                    <div class="orgChildWrap">
                                        <div class="orgNode mid">
                                            <span
                                                class="material-symbols-rounded"
                                                >groups</span
                                            >
                                            Département RH
                                            <div class="nodeTag mid">Admin</div>
                                        </div>
                                        <div class="orgLeaves">
                                            <div class="orgNode leaf">
                                                <span
                                                    class="material-symbols-rounded"
                                                    >person</span
                                                >
                                                Employés
                                            </div>
                                        </div>
                                    </div>
                                    <div class="orgChildWrap">
                                        <div class="orgNode mid">
                                            <span
                                                class="material-symbols-rounded"
                                                >laptop_mac</span
                                            >
                                            Équipe Technique
                                            <div class="nodeTag mid">Admin</div>
                                        </div>
                                        <div class="orgLeaves">
                                            <div class="orgNode leaf">
                                                <span
                                                    class="material-symbols-rounded"
                                                    >person</span
                                                >
                                                Employés
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- FAQ -->
        <section id="faq" class="faqSection">
            <div class="sectionContainer">
                <div class="sectionLabel">FAQ</div>
                <h2 class="sectionTitle">Questions fréquentes</h2>
                <div class="faqGrid">
                    <div
                        class="faqCard"
                        v-for="(item, i) in faqItems"
                        :key="i"
                        @click="toggleFaq(i)"
                        :class="{ open: openFaq === i }"
                    >
                        <div class="faqQuestion">
                            <span>{{ item.q }}</span>
                            <div class="faqToggle">
                                <svg
                                    width="14"
                                    height="14"
                                    viewBox="0 0 14 14"
                                    fill="none"
                                >
                                    <path
                                        d="M7 1v12M1 7h12"
                                        stroke="currentColor"
                                        stroke-width="1.5"
                                        stroke-linecap="round"
                                    />
                                </svg>
                            </div>
                        </div>
                        <div class="faqAnswer">
                            <p>{{ item.a }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA -->
        <section class="ctaSection">
            <div class="ctaInner">
                <div class="ctaBg">
                    <div class="ctaGlow"></div>
                </div>
                <div class="ctaContent">
                    <div class="ctaEyebrow">Commencer gratuitement</div>
                    <h2>Modernisez vos RH<br /><em>dès aujourd'hui.</em></h2>
                    <p>
                        Rejoignez les entreprises qui font confiance à QRCoded
                        pour un pointage sécurisé et sans friction.
                    </p>
                    <div class="ctaBtns">
                        <MainLink
                            v-if="!user"
                            :link="route('user.login')"
                            text="Créer mon compte entreprise"
                        />
                        <MainLink
                            v-else
                            :link="dashboardRoute"
                            text="Ouvrir mon application"
                        />
                    </div>
                </div>
            </div>
        </section>

        <!-- FOOTER -->
        <footer class="landingFooter">
            <div class="footerInner">
                <div class="footerBrand">
                    <div class="brandLogo">
                        <div class="logoMark">
                            <span class="material-symbols-rounded"
                                >qr_code_2</span
                            >
                        </div>
                        <span class="logoText"
                            >QR<span class="weight-thin">Coded</span></span
                        >
                    </div>
                    <p>
                        Infrastructure de présence pour les entreprises
                        modernes.
                    </p>
                </div>
                <div class="footerLinks">
                    <a href="#features">Fonctionnalités</a>
                    <a href="#workflow">Architecture</a>
                    <a href="#faq">FAQ</a>
                </div>
            </div>
            <div class="footerBottom">
                <p>&copy; 2026 QRcoded. Tous droits réservés.</p>
            </div>
        </footer>
    </div>
</template>

<style scoped lang="scss">
@import url("https://fonts.googleapis.com/css2?family=Sora:wght@300;400;500;600;700;800&family=DM+Mono:wght@400;500&display=swap");

/* ===========================
   DESIGN TOKENS
   =========================== */
.homePage {
    --bg: #0a0a0f;
    --surface: #111118;
    --surface2: #16161f;
    --border: rgba(255, 255, 255, 0.08);
    --border-strong: rgba(255, 255, 255, 0.14);
    --text-primary: #f0f0f8;
    --text-secondary: #8888aa;
    --text-muted: #55556a;
    --accent: #4f7cff;
    --accent-dim: rgba(79, 124, 255, 0.15);
    --accent-glow: rgba(79, 124, 255, 0.35);
    --gold: #e8a030;
    --green: #22c97a;
    --amber: #f5a623;
    --purple: #a78bfa;

    font-family: "Sora", sans-serif;
    background: var(--bg);
    color: var(--text-primary);
    width: 100%;
    overflow-x: hidden;
}

* {
    box-sizing: border-box;
}


.heroSection {
    position: relative;
    min-height: calc(100vh - 60px);
    display: flex;
    align-items: center;
    overflow: hidden;
}

.heroBg {
    position: absolute;
    inset: 0;
    pointer-events: none;
}

.gridPattern {
    position: absolute;
    inset: 0;
    background-image:
        linear-gradient(rgba(255, 255, 255, 0.03) 1px, transparent 1px),
        linear-gradient(90deg, rgba(255, 255, 255, 0.03) 1px, transparent 1px);
    background-size: 40px 40px;
    mask-image: radial-gradient(
        ellipse 80% 60% at 50% 0%,
        black 0%,
        transparent 100%
    );
}

.glowOrb {
    position: absolute;
    border-radius: 50%;
    filter: blur(80px);
    &.orb1 {
        width: 600px;
        height: 600px;
        background: radial-gradient(circle, #4f7cff 0%, transparent 70%);
        top: -200px;
        right: -100px;
        opacity: 0.15;
    }
    &.orb2 {
        width: 400px;
        height: 400px;
        background: radial-gradient(circle, #a78bfa 0%, transparent 70%);
        bottom: 50px;
        left: -100px;
        opacity: 0.1;
    }
}

.heroInner {
    max-width: 1200px;
    width: 100%;
    margin: 0 auto;
    padding: 60px 24px 80px;
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 64px;
    align-items: center;
}

.eyebrow {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    font-size: 11px;
    font-weight: 600;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    color: var(--accent);
    margin-bottom: 28px;

    .eyebrowDot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: var(--green);
        animation: blink 2s ease infinite;
    }
}

@keyframes blink {
    0%,
    100% {
        opacity: 1;
    }
    50% {
        opacity: 0.3;
    }
}

h1.heroTitle {
    font-size: clamp(40px, 5vw, 64px);
    font-weight: 800;
    line-height: 1.08;
    letter-spacing: -2px;
    margin: 0 0 24px;
    color: var(--text-primary);
    em {
        font-style: normal;
        color: var(--accent);
    }
}

.heroSub {
    font-size: 16px;
    color: var(--text-secondary);
    line-height: 1.7;
    margin: 0 0 40px;
    max-width: 460px;
}

.heroCtas {
    display: flex;
    align-items: center;
    gap: 16px;
    flex-wrap: wrap;
}

.ghostBtn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    font-size: 14px;
    font-weight: 600;
    color: var(--text-secondary);
    text-decoration: none;
    transition: color 0.15s;
    &:hover {
        color: var(--text-primary);
    }
}

/* DASHBOARD MOCKUP */
.dashMockup {
    background: var(--surface);
    border: 1px solid var(--border-strong);
    border-radius: 14px;
    overflow: hidden;
    box-shadow:
        0 0 0 1px rgba(255, 255, 255, 0.03),
        0 40px 80px rgba(0, 0, 0, 0.6),
        0 0 80px rgba(79, 124, 255, 0.08);
}

.mockHeader {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 16px;
    border-bottom: 1px solid var(--border);
    background: var(--surface2);

    .mockDots {
        display: flex;
        gap: 5px;
        span {
            width: 9px;
            height: 9px;
            border-radius: 50%;
            &:nth-child(1) {
                background: #ff5f57;
            }
            &:nth-child(2) {
                background: #febc2e;
            }
            &:nth-child(3) {
                background: #28c840;
            }
        }
    }
    .mockUrl {
        flex: 1;
        background: rgba(255, 255, 255, 0.06);
        border-radius: 4px;
        padding: 4px 10px;
        font-size: 10px;
        color: var(--text-muted);
        font-family: "DM Mono", monospace;
    }
    .mockStatus {
        display: flex;
        align-items: center;
        gap: 5px;
        font-size: 10px;
        color: var(--green);
        font-weight: 600;
        .pulse {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: var(--green);
            animation: blink 2s ease infinite;
        }
    }
}

.mockBody {
    display: flex;
    height: 320px;
}

.mockSidebar {
    width: 44px;
    background: var(--surface2);
    border-right: 1px solid var(--border);
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 12px 0;
    gap: 4px;

    .mockSideItem {
        width: 32px;
        height: 32px;
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        span {
            font-size: 16px;
            color: var(--text-muted);
        }
        &.active {
            background: var(--accent-dim);
            span {
                color: var(--accent);
            }
        }
    }
}

.mockContent {
    flex: 1;
    padding: 14px;
    overflow: hidden;
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.mockTopRow {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 8px;
}

.mockStatCard {
    border-radius: 8px;
    padding: 10px 12px;
    display: flex;
    flex-direction: column;
    gap: 3px;
    &.stat1 {
        background: rgba(34, 201, 122, 0.1);
    }
    &.stat2 {
        background: rgba(245, 166, 35, 0.1);
    }
    &.stat3 {
        background: rgba(79, 124, 255, 0.1);
    }
    .statVal {
        font-size: 20px;
        font-weight: 700;
        color: var(--text-primary);
        letter-spacing: -0.5px;
    }
    .statLabel {
        font-size: 9px;
        color: var(--text-muted);
        line-height: 1.3;
    }
}

.mockChart {
    background: rgba(255, 255, 255, 0.02);
    border: 1px solid var(--border);
    border-radius: 8px;
    padding: 10px 12px;
    .chartLabel {
        font-size: 10px;
        color: var(--text-muted);
        margin-bottom: 8px;
    }
    .chartBars {
        display: flex;
        align-items: flex-end;
        gap: 5px;
        height: 52px;
    }
    .bar {
        flex: 1;
        background: rgba(79, 124, 255, 0.4);
        border-radius: 3px 3px 0 0;
        height: var(--h);
        position: relative;
        display: flex;
        align-items: flex-end;
        justify-content: center;
        span {
            font-size: 8px;
            color: var(--text-muted);
            position: absolute;
            bottom: -14px;
            font-family: "DM Mono", monospace;
        }
        &.active {
            background: var(--accent);
        }
        &.dim {
            background: rgba(255, 255, 255, 0.08);
        }
    }
}

.mockEmployeeList {
    display: flex;
    flex-direction: column;
    gap: 6px;
    margin-top: 4px;
}

.empRow {
    display: flex;
    align-items: center;
    gap: 8px;
}

.empAvatar {
    width: 26px;
    height: 26px;
    border-radius: 50%;
    font-size: 9px;
    font-weight: 700;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    &.a1 {
        background: rgba(167, 139, 250, 0.2);
        color: #a78bfa;
    }
    &.a2 {
        background: rgba(245, 166, 35, 0.2);
        color: #f5a623;
    }
    &.a3 {
        background: rgba(34, 201, 122, 0.2);
        color: #22c97a;
    }
}

.empInfo {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 1px;
    .empName {
        font-size: 11px;
        font-weight: 600;
        color: var(--text-primary);
    }
    .empTime {
        font-size: 9px;
        color: var(--text-muted);
        font-family: "DM Mono", monospace;
    }
}

.empBadge {
    font-size: 9px;
    font-weight: 700;
    padding: 2px 7px;
    border-radius: 4px;
    &.on-time {
        background: rgba(34, 201, 122, 0.15);
        color: var(--green);
    }
    &.late {
        background: rgba(245, 166, 35, 0.15);
        color: var(--amber);
    }
}

/* ===========================
   METRICS
   =========================== */
.metricsSection {
    border-top: 1px solid var(--border);
    border-bottom: 1px solid var(--border);
    padding: 48px 24px;
    background: var(--surface);
}

.metricsInner {
    max-width: 1000px;
    margin: 0 auto;
    display: flex;
    align-items: center;
    justify-content: center;
}

.metricItem {
    flex: 1;
    text-align: center;
    .metricNum {
        font-size: 36px;
        font-weight: 800;
        letter-spacing: -1.5px;
        color: var(--text-primary);
        line-height: 1;
        margin-bottom: 6px;
    }
    .metricDesc {
        font-size: 12px;
        color: var(--text-muted);
        font-weight: 500;
        line-height: 1.4;
    }
}

.metricDivider {
    width: 1px;
    height: 40px;
    background: var(--border-strong);
    flex-shrink: 0;
    margin: 0 8px;
}

/* ===========================
   FEATURES
   =========================== */
.featuresSection {
    padding: 100px 24px;
}

.sectionContainer {
    max-width: 1200px;
    margin: 0 auto;
}

.sectionLabel {
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 0.1em;
    text-transform: uppercase;
    color: var(--accent);
    margin-bottom: 16px;
}

.sectionTitle {
    font-size: clamp(28px, 4vw, 44px);
    font-weight: 800;
    letter-spacing: -1.5px;
    line-height: 1.1;
    margin: 0 0 56px;
    color: var(--text-primary);
    em {
        font-style: normal;
        color: var(--accent);
    }
}

.featuresGrid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    grid-template-rows: auto auto;
    gap: 16px;
}

.featureCard {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: 12px;
    padding: 28px;
    position: relative;
    overflow: hidden;
    transition:
        border-color 0.2s,
        transform 0.2s;

    &:hover {
        border-color: var(--border-strong);
        transform: translateY(-2px);
    }

    &.large {
        grid-column: span 2;
        display: flex;
        gap: 28px;
        align-items: flex-start;
        background: linear-gradient(
            135deg,
            var(--surface) 0%,
            rgba(79, 124, 255, 0.08) 100%
        );
        border-color: rgba(79, 124, 255, 0.2);
        .featureBody {
            flex: 1;
        }
        .featureAccent {
            position: absolute;
            top: 0;
            right: 0;
            width: 200px;
            height: 200px;
            background: radial-gradient(
                circle,
                rgba(79, 124, 255, 0.12) 0%,
                transparent 70%
            );
            pointer-events: none;
        }
    }

    .featureIcon {
        width: 44px;
        height: 44px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 20px;
        background: var(--accent-dim);
        flex-shrink: 0;
        span {
            font-size: 20px;
            color: var(--accent);
        }
        &.amber {
            background: rgba(245, 166, 35, 0.12);
            span {
                color: var(--amber);
            }
        }
        &.blue {
            background: rgba(79, 124, 255, 0.12);
            span {
                color: #7ba3ff;
            }
        }
        &.green {
            background: rgba(34, 201, 122, 0.12);
            span {
                color: var(--green);
            }
        }
        &.purple {
            background: rgba(167, 139, 250, 0.12);
            span {
                color: var(--purple);
            }
        }
    }

    h3 {
        font-size: 16px;
        font-weight: 700;
        margin: 0 0 10px;
        color: var(--text-primary);
        letter-spacing: -0.3px;
    }
    p {
        font-size: 13.5px;
        color: var(--text-secondary);
        line-height: 1.65;
        margin: 0;
    }
}

/* ===========================
   WORKFLOW
   =========================== */
.workflowSection {
    padding: 100px 24px;
    background: var(--surface);
    border-top: 1px solid var(--border);
}

.workflowGrid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 80px;
    align-items: center;
}

.workflowIntro {
    font-size: 15px;
    color: var(--text-secondary);
    line-height: 1.7;
    margin: 0 0 40px;
}

.rolesStack {
    display: flex;
    flex-direction: column;
}

.roleItem {
    display: flex;
    gap: 16px;
    align-items: flex-start;
}

.roleLine {
    width: 1px;
    height: 24px;
    background: var(--border-strong);
    margin: 6px 0 6px 19px;
}

.roleIconWrap {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    border: 1px solid var(--border);
    span {
        font-size: 18px;
    }
    &.gold {
        background: rgba(232, 160, 48, 0.1);
        span {
            color: var(--gold);
        }
    }
    &.dark {
        background: rgba(255, 255, 255, 0.05);
        span {
            color: var(--text-primary);
        }
    }
    &.blue {
        background: var(--accent-dim);
        span {
            color: var(--accent);
        }
    }
}

.roleContent {
    h4 {
        font-size: 14px;
        font-weight: 700;
        margin: 0 0 4px;
        color: var(--text-primary);
        letter-spacing: -0.2px;
    }
    p {
        font-size: 13px;
        color: var(--text-secondary);
        line-height: 1.6;
        margin: 0;
    }
}

.orgChart {
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 32px;
    background: var(--bg);
    border: 1px solid var(--border);
    border-radius: 16px;
}

.orgNode {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 4px;
    padding: 12px 20px;
    border-radius: 10px;
    font-size: 12px;
    font-weight: 600;
    border: 1px solid var(--border);
    text-align: center;
    position: relative;
    span.material-symbols-rounded {
        font-size: 18px;
        margin-bottom: 2px;
    }
    &.root {
        background: var(--accent);
        border-color: var(--accent);
        color: #fff;
        min-width: 200px;
        span.material-symbols-rounded {
            color: rgba(255, 255, 255, 0.8);
        }
    }
    &.mid {
        background: var(--surface2);
        color: var(--text-primary);
        min-width: 130px;
        span.material-symbols-rounded {
            color: var(--text-secondary);
        }
    }
    &.leaf {
        background: rgba(79, 124, 255, 0.08);
        color: var(--text-secondary);
        min-width: 110px;
        border-color: rgba(79, 124, 255, 0.15);
        span.material-symbols-rounded {
            color: var(--accent);
            font-size: 14px;
        }
    }
}

.nodeTag {
    font-size: 9px;
    font-weight: 700;
    letter-spacing: 0.05em;
    text-transform: uppercase;
    background: rgba(255, 255, 255, 0.15);
    color: rgba(255, 255, 255, 0.7);
    padding: 2px 8px;
    border-radius: 4px;
    &.mid {
        background: rgba(79, 124, 255, 0.15);
        color: var(--accent);
    }
}

.orgBranch {
    display: flex;
    flex-direction: column;
    align-items: center;
    width: 100%;
}
.orgBranchLine {
    width: 1px;
    height: 20px;
    background: var(--border-strong);
}

.orgChildren {
    display: flex;
    gap: 24px;
    align-items: flex-start;
    position: relative;
    &::before {
        content: "";
        position: absolute;
        top: 0;
        left: calc(25% + 12px);
        right: calc(25% + 12px);
        height: 1px;
        background: var(--border-strong);
    }
}

.orgChildWrap {
    display: flex;
    flex-direction: column;
    align-items: center;
}

.orgLeaves {
    display: flex;
    flex-direction: column;
    align-items: center;
    &::before {
        content: "";
        width: 1px;
        height: 16px;
        background: var(--border-strong);
        display: block;
    }
}

/* ===========================
   FAQ
   =========================== */
.faqSection {
    padding: 100px 24px;
    border-top: 1px solid var(--border);
}

.faqGrid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 12px;
}

.faqCard {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: 10px;
    overflow: hidden;
    cursor: pointer;
    transition: border-color 0.15s;
    &:hover,
    &.open {
        border-color: var(--border-strong);
    }
    &.open .faqToggle {
        transform: rotate(45deg);
        background: var(--accent-dim);
        border-color: rgba(79, 124, 255, 0.2);
        svg path {
            stroke: var(--accent);
        }
    }
    &.open .faqAnswer {
        max-height: 200px;
        padding: 0 20px 16px;
    }
}

.faqQuestion {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    padding: 16px 20px;
    span {
        font-size: 14px;
        font-weight: 600;
        color: var(--text-primary);
        line-height: 1.4;
    }
}

.faqToggle {
    width: 28px;
    height: 28px;
    border-radius: 6px;
    border: 1px solid var(--border);
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    transition: all 0.2s;
    svg path {
        stroke: var(--text-muted);
    }
}

.faqAnswer {
    max-height: 0;
    overflow: hidden;
    transition:
        max-height 0.3s ease,
        padding 0.3s ease;
    p {
        font-size: 13px;
        color: var(--text-secondary);
        line-height: 1.65;
        margin: 0;
        border-top: 1px solid var(--border);
        padding-top: 14px;
    }
}

/* ===========================
   CTA
   =========================== */
.ctaSection {
    padding: 80px 24px;
    position: relative;
    overflow: hidden;
}

.ctaInner {
    max-width: 1200px;
    margin: 0 auto;
    position: relative;
}

.ctaBg {
    position: absolute;
    inset: 0;
    border-radius: 20px;
    background: var(--surface);
    border: 1px solid rgba(79, 124, 255, 0.2);
    overflow: hidden;
    .ctaGlow {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 600px;
        height: 400px;
        background: radial-gradient(
            ellipse,
            rgba(79, 124, 255, 0.15) 0%,
            transparent 70%
        );
    }
}

.ctaContent {
    position: relative;
    padding: 80px 48px;
    text-align: center;
    display: flex;
    flex-direction: column;
    align-items: center;
    .ctaEyebrow {
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 0.1em;
        text-transform: uppercase;
        color: var(--accent);
        margin-bottom: 20px;
    }
    h2 {
        font-size: clamp(32px, 4vw, 52px);
        font-weight: 800;
        letter-spacing: -2px;
        line-height: 1.1;
        margin: 0 0 16px;
        color: var(--text-primary);
        em {
            font-style: normal;
            color: var(--accent);
        }
    }
    p {
        font-size: 15px;
        color: var(--text-secondary);
        max-width: 480px;
        line-height: 1.65;
        margin: 0 0 36px;
    }
}

/* ===========================
   FOOTER
   =========================== */
.landingFooter {
    border-top: 1px solid var(--border);
    background: var(--surface);
    padding: 48px 24px 32px;
}

.footerInner {
    max-width: 1200px;
    margin: 0 auto 32px;
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    .footerBrand {
        p {
            font-size: 13px;
            color: var(--text-muted);
            margin: 12px 0 0;
            max-width: 260px;
            line-height: 1.5;
        }
    }
    .footerLinks {
        display: flex;
        gap: 28px;
        align-items: center;
        margin-top: 4px;
        a {
            font-size: 13px;
            color: var(--text-muted);
            text-decoration: none;
            transition: color 0.15s;
            font-weight: 500;
            &:hover {
                color: var(--text-secondary);
            }
        }
    }
}

.brandLogo {
    display: flex;
    align-items: center;
    gap: 10px;
    .logoMark {
        width: 32px;
        height: 32px;
        background: #4f7cff;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        span {
            font-size: 17px;
            color: #fff;
        }
    }
    .logoText {
        font-size: 16px;
        font-weight: 700;
        letter-spacing: -0.3px;
        color: var(--text-primary);
    }
    .weight-thin {
        font-weight: 300;
        color: var(--text-secondary);
    }
}

.footerBottom {
    max-width: 1200px;
    margin: 0 auto;
    border-top: 1px solid var(--border);
    padding-top: 20px;
    p {
        font-size: 12px;
        color: var(--text-muted);
        margin: 0;
    }
}

/* ===========================
   RESPONSIVE
   =========================== */
@media (max-width: 960px) {
    .heroInner {
        grid-template-columns: 1fr;
        padding: 40px 24px 60px;
        gap: 48px;
    }
    .workflowGrid {
        grid-template-columns: 1fr;
    }
    .featuresGrid {
        grid-template-columns: 1fr;
    }
    .featuresGrid .featureCard.large {
        grid-column: span 1;
        flex-direction: column;
    }
    .faqGrid {
        grid-template-columns: 1fr;
    }
    .metricsInner {
        flex-wrap: wrap;
        gap: 32px;
        .metricDivider {
            display: none;
        }
        .metricItem {
            flex: 0 0 calc(50% - 16px);
        }
    }
    .footerInner {
        flex-direction: column;
        gap: 32px;
    }
}

@media (max-width: 640px) {
    .heroInner {
        padding: 32px 20px 60px;
    }
    h1.heroTitle {
        font-size: 36px;
        letter-spacing: -1px;
    }
    .metricsInner .metricItem {
        flex: 0 0 100%;
    }
    .ctaContent {
        padding: 48px 24px;
    }
}
</style>
