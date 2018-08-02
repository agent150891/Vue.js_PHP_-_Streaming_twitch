export const getters = {
    checkToken: state => {
        return state.token ? true : false;
    },
    profileData: state => {
        return state.profileData;
    },
    promotedStreamers: state => {
        return state.promotedStreamers.list;
    },
    promotedLoaded: state => {
        return state.promotedStreamers.loaded;
    },
    currentViewer: state => {
        return state.currentViewer;
    },
    subscriptionPlans: state => {
        return state.subscriptionPlans.list;
    },
    monthPlans: state => {
        return state.monthPlans.list;
    },
    currentStreamer: state => {
        return state.currentStreamer;
    },
    myStreamers: state => {
        return state.myStreamers.list;
    },
    myViewers: state => {
        return state.myViewers.list;
    },
    afiliates: state => {
        return state.afiliates;
    },
    afiliateLink: state => {
        return state.afiliateLink;
    },
    games: state => {
        return state.games.list;
    },
    streamers: state => {
        return state.streamers.list;
    },
    streamersLoaded: state => {
        return state.streamers.loaded;
    },
    notifications: state => {
        return state.notifications.list;
    },
    achivements: state => {
        return state.achivements.list;
    },
    streamerFullData: state => {
        return state.streamerFullData;
    },
    mainContent: state => {
        return state.mainContent.list;
    },
    mainContentLoaded: state => {
        return state.mainContent.loaded;
    },
    mainChannel: state => {
        return state.mainChannel;
    },
    wachingStreamers: state => {
        return state.wachingStreamers;
    },
    menuMessages: state => {
        return state.menuMessages;
    },
}