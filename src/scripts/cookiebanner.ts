type ComplzlocalStorage = {
    statistics: boolean;
    storage: boolean;
    timestamp?: number;
    answered: boolean;
}

type ComplzLocalStorageKey = keyof ComplzlocalStorage;


export interface Subject {
    // Attach an observer to the subject.
    attach(observer: Observer): void;

    // Detach an observer from the subject.
    detach(observer: Observer): void;

    // Notify all observers about an event.
    notify(): void;
}

export interface Observer {
    // Receive update from subject.
    update(subject: Subject): void;
}

    export class CookieBannerObserver implements Observer {
        update(subject: Subject) {
            console.log("update called from within observer", subject)
        }
    } 

export class MyComplz implements Subject{
    // handles checks for granted permissions for cookies and co.
    // get & setter for localstorage
    static #instance: MyComplz;

    private observers: Observer[] = [];

    private $storage: ComplzlocalStorage = {
        statistics: false,
        storage: false,
        timestamp: undefined,
        answered: false
    } 

    private constructor() {
        // read values or default
        this.initFromLocalstorage()
        this.installWindowListeners()
    }

    attach(observer: Observer) {
        const isExist = this.observers.includes(observer);
        if (isExist) {
            return console.log('Subject: Observer has been attached already.');
        }

        console.log('Subject: Attached an observer.');
        this.observers.push(observer);
    }

    public detach(observer: Observer): void {
        const observerIndex = this.observers.indexOf(observer);
        if (observerIndex === -1) {
            return console.log('Subject: Nonexistent observer.');
        }

        this.observers.splice(observerIndex, 1);
        console.log('Subject: Detached an observer.');
    }

    /**
     * Trigger an update in each subscriber.
     */
    public notify(): void {
        console.log('Subject: Notifying observers...');
        for (const observer of this.observers) {
            observer.update(this);
        }
    }

    public static get instance() :MyComplz  {
        if (!MyComplz.#instance) {
            MyComplz.#instance = new MyComplz();
        }

        return MyComplz.#instance;
    }

    installWindowListeners() {
        window.addEventListener("cmplz-changed", () => {
            console.log("complz changed")
        })
    }

    initFromLocalstorage() {
        (Object.keys(this.$storage) as ComplzLocalStorageKey[]).forEach((type) => {
            let typeSetting = window.localStorage.getItem(type)
            if (typeSetting !== null) {
                if (type === "statistics" || type === "storage" || type === "answered" ) {
                    this.$storage[type] = (typeSetting == "true") ? true : false;
                }
                if (type === "timestamp") {
                    this.$storage[type] = Number.parseInt(typeSetting)
                }
            } else {
                if (type === "statistics" || type === "storage" || type === "answered" ) {
                    this.$storage[type] = false;
                }

            }
        })
        this.checkLastAnsweredTime()
        this.notify()
    }

    checkLastAnsweredTime() {
            let diff = ((Math.floor(Date.now() / 1000) - Math.floor(this.getTimestamp() / 1000)));
            const MINUTE = 60;
            const HOUR = MINUTE * 60;
            const DAY = HOUR * 24;

            // if (diff > DAY * 7) {
            if (diff > MINUTE * 5) {
                console.log("TIMEOUT new permission")
                this.deleteLocalStorage()
                this.resetStorage()
            }
    }

    getStorageValue(key: keyof ComplzlocalStorage) {
        return this.$storage[key];
    }

    getStatistics() {
        return this.$storage.statistics;
    }

    getStorage() {
        return this.$storage.storage;
    }

    setStatistics(payload: boolean) :void {
        this.$storage.statistics = payload;
    }

    setStorage(payload: boolean) :void {
        this.$storage.storage = payload;
    }

    getAnswered() :boolean {
        return this.$storage.answered;
    }

    setAnswered(p: boolean) {
        this.$storage.answered = p;
    }

    getTimestamp() :number {
        return this.$storage.timestamp ?? Date.now();
    }

    saveToLocalstorage() {
        this.setAnswered(true);
        (Object.keys(this.$storage) as ComplzLocalStorageKey[]).forEach((type) => {
            console.log(type)
                if (type === "statistics" || type === "storage" || type === "answered" ) {
                    let res: boolean = this.$storage[type];
                    window.localStorage.setItem(type, `${res}`)
                }
                if (type === "timestamp") {
                    let res: number = this.getTimestamp();
                    window.localStorage.setItem(type, `${res}`)
                }
        })
        this.notify()
    }

    deleteLocalStorage() {
        (Object.keys(this.$storage) as ComplzLocalStorageKey[]).forEach((type) => {
            window.localStorage.removeItem(type)
        })
    }

    resetStorage() {
        this.setAnswered(false)
        this.setStatistics(false)
        this.setStorage(false)
    }

    print() {
        // (Object.keys(this.$localStorage) as ComplzLocalStorageKey[]).forEach((type) => {
        //     console.log(type)
        //     let typeSetting = window.localStorage.getItem(type)
        //     if (typeSetting !== null) {
        //         this.$localStorage[type] = (typeSetting == "true") ? true : false;
        //     }
        // })
        console.log(this.$storage)
    }

    openCookieBanner() {
        const event = new CustomEvent('open-banner')
        window.dispatchEvent(event)
    }
}

if (!window.Complianz) {
    window.Complianz = MyComplz.instance;
}

// const plz = new MyComplz();
// plz.print()
// plz.saveToLocalstorage()

// plz.setStatistics(true)
// plz.saveToLocalstorage()