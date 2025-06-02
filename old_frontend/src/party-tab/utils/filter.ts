import GameSession from "../../types/GameSession";

// Function to group by date
export const groupByDate = (array) => {
  return array.reduce((result, session) => {
    const date = session.date;
    if (!result[date]) {
      result[date] = [];
    }
    result[date].push(session);
    return result;
  }, {});
};

// Function to group by week
export const groupByWeek = (sessions: GameSession[]) => {
  return sessions.reduce((result, session) => {
    const date = new Date(session.date);
    const year = date.getFullYear();
    const week = Math.floor((date.getTime() - new Date(year, 0, 1).getTime()) / (7 * 24 * 60 * 60 * 1000));
    const key = `${year}-W${week}`;
    if (!result[key]) {
      const weekStart = new Date(date);
      weekStart.setDate(date.getDate() - date.getDay());
      result[key] = { weekStart, sessions: [] };
    }
    result[key].sessions.push(session);
    return result;
  }, {});
};

// Function to group by month
export const groupByMonth = (sessions: GameSession[]) => {
  return sessions.reduce((result, session) => {
    const date = new Date(session.date);
    const key = `${date.getFullYear()}-${date.getMonth() + 1}`;
    if (!result[key]) {
      result[key] = { month: date.getMonth(), year: date.getFullYear(), sessions: [] };
    }
    result[key].sessions.push(session);
    return result;
  }, {});
};

// Function to group by location
export const groupByLocation = (sessions: GameSession[]) => {
  return sessions.reduce((result, session) => {
    const location = session.lieu || 'Unknown';
    if (!result[location]) {
      result[location] = { location, sessions: [] };
    }
    result[location].sessions.push(session);
    return result;
  }, {});
};

export const groupByType = (sessions: GameSession[]) => {
  return sessions.reduce((result, session) => {
    const type = session.type || 'Unknown';
    if (!result[type]) {
      result[type] = { type, sessions: [] };
    }
    result[type].sessions.push(session);
    return result;
  }, {});
};

export const groupByCategorie = (sessions: GameSession[]) => {
  return sessions.reduce((result, session) => {
    const location = session.lieu || 'Unknown';
    if (!result[location]) {
      result[location] = { location, sessions: [] };
    }
    result[location].sessions.push(session);
    return result;
  }, {});
};